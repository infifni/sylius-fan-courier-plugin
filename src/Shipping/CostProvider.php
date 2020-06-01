<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Shipping;

use Exception;
use Infifni\SyliusFanCourierPlugin\Api\ClientIntermediate;
use Infifni\SyliusFanCourierPlugin\Api\RequestFormatter;
use Infifni\SyliusFanCourierPlugin\Exception\CostEstimationNotNumericException;
use Infifni\SyliusFanCourierPlugin\Exception\WrongCityNameException;
use Infifni\SyliusFanCourierPlugin\Exception\WrongProvinceNameException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CostProvider
{
    /**
     * @var RequestFormatter
     */
    private $requestFormatter;

    /**
     * @var array
     */
    private $shippingGatewayConfig;

    /**
     * @var ClientIntermediate
     */
    private $clientIntermediate;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        RequestFormatter $requestFormatter,
        GatewayConfigProvider $gatewayConfigProvider,
        ClientIntermediate $clientIntermediate,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->shippingGatewayConfig = $gatewayConfigProvider->get();
        $this->clientIntermediate = $clientIntermediate;
        $this->requestFormatter = $requestFormatter;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param string $messageId
     */
    public function addErrorFlash(
        string $messageId = 'infifni.sylius_fan_courier_plugin.ui.errors.wrong_shipping_cost'
    ): void {
        $message = $this->translator->trans($messageId);

        if (false === $this->flashBag->has('error')) {
            $this->flashBag->add('error', $message);
        }
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @return int
     * @throws InvalidArgumentException
     */
    public function getCost(ShipmentInterface $shipment): int
    {
        $initialCost = $this->getCostIndependentOfApi($shipment);
        /** @var Order $order */
        $order = $shipment->getOrder();
        if (! $order->getShippingAddress()) {
            return (int) $initialCost;
        }

        if (null === $initialCost) {
            return $this->getCostDependentOfApi($shipment);
        }

        return $initialCost;
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @return int|null
     */
    private function getCostIndependentOfApi(ShipmentInterface $shipment): ?int
    {
        $cost = null;
        if ($this->shippingGatewayConfig['hide_shipping_cost']) {
            $cost = 0;
        } elseif ($this->shippingGatewayConfig['free_shipping_min_value']) {
            $order = $shipment->getOrder();
            if ($order && $order->getTotal() / 100 >= $this->shippingGatewayConfig['free_shipping_min_value']) {
                $cost = 0;
            } elseif ($this->shippingGatewayConfig['fixed_cost']) {
                $cost = (int) $this->shippingGatewayConfig['fixed_cost'] * 100;
            }
        }

        return $cost;
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @return int|null
     * @throws InvalidArgumentException
     */
    private function getCostDependentOfApi(ShipmentInterface $shipment): int
    {
        $this->requestFormatter->setShipment($shipment)->setProvince()->setCities();

        try {
            $params = $this->requestFormatter->getPriceRequestParams();
            $response = $this->clientIntermediate->fanClient->price($params);
            if (false !== strpos($response, 'Error nume localitate destinatie (2).')) {
                throw new WrongCityNameException($params['localitate_dest']);
            }
            if (! is_numeric($response)) {
                throw new CostEstimationNotNumericException($response);
            }

            return (float) $response * 100;
        } catch (Exception $e) {
            $this->logger->error(
                "Shipping estimation failed for shipment with id {$shipment->getId()}: {$e->getMessage()}"
            );

            if ($e instanceof WrongProvinceNameException || $e instanceof CostEstimationNotNumericException) {
                $this->addErrorFlash();
            }
            if ($e instanceof WrongCityNameException) {
                $this->addErrorFlash('infifni.sylius_fan_courier_plugin.ui.errors.wrong_city_name');
            }

            return 0;
        }
    }
}