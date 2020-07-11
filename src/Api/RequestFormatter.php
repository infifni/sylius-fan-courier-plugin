<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Api;

use Infifni\FanCourierApiClient\Request\City;
use Infifni\FanCourierApiClient\Request\Price;
use Infifni\SyliusFanCourierPlugin\Exception\WrongProvinceNameException;
use Infifni\SyliusFanCourierPlugin\Shipping\GatewayConfigProvider;
use Psr\Cache\InvalidArgumentException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class RequestFormatter
{
    private const CITY_DATA_BASE_KEY = 'infifni_sylius_fan_courier_plugin_city_data_key_';
    private const PRICE_ESTIMATION = 'price_estimation';
    private const AWB_GENERATOR = 'awb_generator';

    /**
     * @var array
     */
    private $cities;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var ClientIntermediate
     */
    private $clientIntermediate;

    /**
     * @var Shipment|ShipmentInterface
     */
    private $shipment;

    /**
     * @var Province
     */
    private $province;

    /**
     * @var string
     */
    private $provinceCleanName;

    /**
     * @var array
     */
    private $shippingGatewayConfig;

    /**
     * @var EntityRepository
     */
    private $provinceRepository;

    public function __construct(
        AdapterInterface $cache,
        ClientIntermediate $clientIntermediate,
        GatewayConfigProvider $gatewayConfigProvider,
        EntityRepository $provinceRepository
    ) {
        $this->cache = $cache;
        $this->clientIntermediate = $clientIntermediate;
        $this->shippingGatewayConfig = $gatewayConfigProvider->get();
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @return self
     */
    public function setShipment(ShipmentInterface $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * @return self
     */
    public function setProvince(): self
    {
        $this->province = $this->provinceRepository->findOneBy([
            'code' => $this->shipment->getOrder()->getShippingAddress()->getProvinceCode()
        ]);

        return $this;
    }

    public function getProvinceCleanName(): string
    {
        if (null === $this->provinceCleanName) {
            $this->provinceCleanName = $this->cleanString($this->province->getName());
        }

        return $this->provinceCleanName;
    }

    /**
     * @param string $string
     * @return string|string[]
     */
    public function cleanString(string $string)
    {
        return ucwords(strtolower(str_replace(
                ['ș', 'ț', 'î', 'ă', 'â'],
                ['s', 't', 'i', 'a', 'a'],
                $string
        )), ' -');
    }

    /**
     * @return self
     * @throws InvalidArgumentException
     */
    public function setCities(): self
    {
        $item = $this->cache->getItem(
            self::CITY_DATA_BASE_KEY.$this->getProvinceCleanName()
        );
        if (! $item->isHit()) {
            $cityData = $this->clientIntermediate->fanClient->city([
                'language' => City::LANGUAGE_EN_ALLOWED_VALUE,
                'judet' => $this->getProvinceCleanName()
            ]);
            $item->set($cityData);
            if ($cityData) {
                $this->cache->save($item);
            }
        }
        $this->cities = $item->get();

        return $this;
    }

    /**
     * @throws WrongProvinceNameException
     */
    public function getPriceRequestParams(): array
    {
        if (! $this->cities) {
            throw new WrongProvinceNameException($this->province);
        }

        $order = $this->shipment->getOrder();
        $params = [];
        $this->prepareSimilarParams($order, $params, self::PRICE_ESTIMATION);

        return $params;
    }

    /**
     * @throws WrongProvinceNameException
     */
    public function getGenerateAwbRequestParams(): array
    {
        if (! $this->cities) {
            throw new WrongProvinceNameException($this->province);
        }

        $order = $this->shipment->getOrder();

        $address = $order->getShippingAddress();
        $customer = $order->getCustomer();
        $length = $this->shipment->getShippingVolume() ** (1/3);
        $params = [
            'tip_serviciu' => $this->shippingGatewayConfig['active_service'],
            'plata_expeditie' => $this->shippingGatewayConfig['who_pays_awb'],
            'ramburs_bani' => $order->getTotal() / 100,
            'persoana_contact_expeditor' => $this->shippingGatewayConfig['sender_contact_person'],
            'observatii' => $this->shippingGatewayConfig['observations'],
            'nume_destinatar' => $address->getLastName() . ' ' . $address->getFirstName(),
            'persoana_contact' => $address->getLastName() . ' ' . $address->getFirstName(),
            'telefon' => $address->getPhoneNumber(),
            'email' => $customer->getEmail(),
            'judet' => $this->getProvinceCleanName(),
            'strada' => $address->getStreet(),
            'cod_postal' => str_pad($address->getPostcode(), 6 ,'0'),
            'greutate' => $this->shipment->getShippingWeight(),
            'lungime_pachet' => $length,
            'latime_pachet' => $length,
            'inaltime_pachet' => $length
        ];

        if ($this->shippingGatewayConfig['with_product_codes_in_content']) {
            $params['continut'] = '';
            foreach ($order->getItems() as $item) {
                $newContent = $params['continut'];
                if ($newContent) {
                    $newContent .= ', ';
                }
                $newContent .= $item->getQuantity() * $item->getVariant()->getWeight().'x'.$item->getProduct()->getId();
                if (strlen($newContent) <= 36) {
                    $params['continut'] = $newContent;
                } else {
                    break;
                }
            }
        }

        $this->prepareSimilarParams($order, $params, self::AWB_GENERATOR);

        return $params;
    }

    private function prepareSimilarParams(
        OrderInterface $order,
        array &$params,
        $forWhat = self::PRICE_ESTIMATION
    ): void {
        switch ($forWhat) {
            case self::PRICE_ESTIMATION:
                $activeServiceKey = 'serviciu';
                $cityKey = 'localitate_dest';
                $countyKey = 'judet_dest';
                $declaredValueKey = 'val_decl';
                $parcelsKey = 'colete';
                $envelopesKey = 'plicuri';
                $lengthKey = 'lungime';
                $widthKey = 'latime';
                $heightKey = 'inaltime';
                $whoPaysRepaymentKey = 'plata_ramburs';

                break;
            case self::AWB_GENERATOR:
                $activeServiceKey = 'tip_serviciu';
                $cityKey = 'localitate';
                $countyKey = 'judet';
                $parcelsKey = 'nr_colete';
                $envelopesKey = 'nr_plicuri';
                $declaredValueKey = 'valoare_declarata';
                $lengthKey = 'lungime_pachet';
                $widthKey = 'latime_pachet';
                $heightKey = 'inaltime_pachet';
                $whoPaysRepaymentKey = 'plata_ramburs_la';

                break;

            default:
                return;
        }

        $params['greutate'] = $this->shipment->getShippingWeight();
        $nbPackages = $this->shippingGatewayConfig['number_of_packages'] ?: 1;
        if ($params['greutate'] > 1) {
            $params[$parcelsKey] = $nbPackages;
            $params[$envelopesKey] = 0;
        } elseif ($this->shippingGatewayConfig['parcels_or_envelopes']) {
            $params[$parcelsKey] = $nbPackages;
            $params[$envelopesKey] = 0;
        } else {
            $params[$envelopesKey] = $nbPackages;
            $params[$parcelsKey] = 0;
        }

        if ($this->shippingGatewayConfig['with_assurance']) {
            $params[$declaredValueKey] = $order->getTotal() / 100;
        }

        $params['optiuni'] = '';
        if ($this->shippingGatewayConfig['open_allowed']) {
            $params['optiuni'] .= 'A';
        }
        if ($this->shippingGatewayConfig['epod']) {
            $params['optiuni'] .= 'X';
        }

        $address = $order->getShippingAddress();
        $length = $this->shipment->getShippingVolume() ** (1/3);
        $params[$activeServiceKey] = $this->shippingGatewayConfig['active_service'];
        $params[$cityKey] = $this->cleanString($address->getCity());
        $params[$countyKey] = $this->getProvinceCleanName();
        $params[$lengthKey] = $length;
        $params[$widthKey] = $length;
        $params[$heightKey] = $length;
        $params[$whoPaysRepaymentKey] = $this->shippingGatewayConfig['who_pays_repayment'];
        if ($this->shippingGatewayConfig['add_shipping_cost_to_repayment']) {
            $params[$whoPaysRepaymentKey] = Price::SENDER_ALLOWED_VALUE;
        }
    }
}