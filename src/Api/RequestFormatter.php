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
use Infifni\SyliusFanCourierPlugin\Exception\WrongProvinceNameException;
use Infifni\SyliusFanCourierPlugin\Shipping\GatewayConfigProvider;
use Psr\Cache\InvalidArgumentException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class RequestFormatter
{
    private const CITY_DATA_BASE_KEY = 'infifni_sylius_fan_courier_plugin_city_data_key_';

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
            $this->provinceCleanName = $this->stripDiacritics($this->province->getName());
        }

        return $this->provinceCleanName;
    }

    /**
     * @param string $string
     * @return string|string[]
     */
    private function stripDiacritics(string $string)
    {
        return str_replace(
            ['ș', 'ț', 'î', 'ă', 'â'],
            ['s', 't', 'i', 'a', 'a'],
            $string
        );
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
        $address = $order->getShippingAddress();
        $length = $this->shipment->getShippingVolume() ** (1/3);
        $params = [
            'serviciu' => $this->shippingGatewayConfig['active_service'],
            'localitate_dest' => $this->stripDiacritics($address->getCity()),
            'judet_dest' => $this->getProvinceCleanName(),
            'greutate' => $this->shipment->getShippingWeight(),
            'lungime' => $length,
            'latime' => $length,
            'inaltime' => $length,
            'plata_ramburs' => $this->shippingGatewayConfig['who_pays_repayment']
        ];

        $nbPackages = $this->shippingGatewayConfig['number_of_packages'] ?: 1;
        if ($params['greutate'] > 1) {
            $params['colete'] = $nbPackages;
            $params['plicuri'] = 0;
        } elseif ($this->shippingGatewayConfig['parcels_or_envelopes']) {
            $params['colete'] = $nbPackages;
            $params['plicuri'] = 0;
        } else {
            $params['plicuri'] = $nbPackages;
            $params['colete'] = 0;
        }

        if ($this->shippingGatewayConfig['with_assurance']) {
            $params['val_decl'] = $order->getTotal() / 100;
        }

        return $params;
    }
}