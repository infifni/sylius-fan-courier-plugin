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
use Infifni\SyliusFanCourierPlugin\Exception\ExpectedAwbException;
use Infifni\SyliusFanCourierPlugin\Exception\WrongCityNameException;
use Infifni\SyliusFanCourierPlugin\Exception\WrongProvinceNameException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AwbGenerator
{
    /**
     * @var RequestFormatter
     */
    private $requestFormatter;

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
        ClientIntermediate $clientIntermediate,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->clientIntermediate = $clientIntermediate;
        $this->requestFormatter = $requestFormatter;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param string $messageId
     * @param array $params
     */
    public function addErrorFlash(
        string $messageId = 'infifni.sylius_fan_courier_plugin.ui.errors.awb_generation.wrong_province_name',
        array $params = []
    ): void {
        $message = $this->translator->trans($messageId, $params);

        if (false === $this->flashBag->has('error')) {
            $this->flashBag->add('error', $message);
        }
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function generateAwb(ShipmentInterface $shipment): ?array
    {
        $this->requestFormatter->setShipment($shipment)->setProvince()->setCities();
        $shipmentAddress = $shipment->getOrder()->getShippingAddress();

        try {
            $params = $this->requestFormatter->getGenerateAwbRequestParams();
            $response = $this->clientIntermediate->fanClient->generateAwb([
                'fisier' => [
                    $params
                ]
            ]);

            $responseData = $response[0];
            if (false !== strpos($responseData['error_message'], 'Probleme la localitate')) {
                throw new WrongCityNameException($params['localitate']);
            }
            if (false === $responseData['awb']) {
                throw new ExpectedAwbException($responseData['error_message']);
            }

            return $responseData;
        } catch (Exception $e) {
            $this->logger->error(
                "Shipping awb generation failed for shipment with id {$shipment->getId()}: {$e->getMessage()}"
            );

            if ($e instanceof WrongProvinceNameException) {
                $this->addErrorFlash(
                    'infifni.sylius_fan_courier_plugin.ui.errors.awb_generation.wrong_province_name',
                    [
                        '%county%' => $this->requestFormatter->getProvinceCleanName(),
                        '%order_number%' => $shipment->getOrder()->getNumber()
                    ]
                );
            } elseif ($e instanceof WrongCityNameException) {
                $this->addErrorFlash(
                    'infifni.sylius_fan_courier_plugin.ui.errors.awb_generation.wrong_city_name',
                    [
                        '%city%' => $this->requestFormatter->cleanString($shipmentAddress->getCity()),
                        '%order_number%' => $shipment->getOrder()->getNumber()
                    ]
                );
            } elseif ($e instanceof ExpectedAwbException) {
                $this->addErrorFlash(
                    'infifni.sylius_fan_courier_plugin.ui.errors.awb_generation.missing_awb',
                    [
                        '%order_number%' => $shipment->getOrder()->getNumber()
                    ]
                );
            } else {
                $this->addErrorFlash(
                    'infifni.sylius_fan_courier_plugin.ui.errors.awb_generation.unknown_internal_error',
                    [
                        '%city%' => $this->requestFormatter->cleanString($shipmentAddress->getCity()),
                        '%order_number%' => $shipment->getOrder()->getNumber()
                    ]
                );
            }

            return null;
        }
    }
}