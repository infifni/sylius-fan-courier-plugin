<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\EventListener;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Infifni\SyliusFanCourierPlugin\Api\ClientIntermediate;
use Infifni\SyliusFanCourierPlugin\Entity\ShippingAwb;
use Infifni\SyliusFanCourierPlugin\Form\Type\ShippingGatewayType;
use Infifni\SyliusFanCourierPlugin\Repository\ShippingAwbRepository;
use Infifni\SyliusFanCourierPlugin\Shipping\AwbGenerator;
use Infifni\SyliusFanCourierPlugin\Shipping\CostProvider;
use JsonException;
use Psr\Cache\InvalidArgumentException;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

class ShippingExportEventListener
{
    /**
     * @var AwbGenerator
     */
    private $awbGenerator;

    /**
     * @var ShippingAwbRepository
     */
    private $awbRepository;

    /**
     * @var ClientIntermediate
     */
    private $clientIntermediate;

    /**
     * @var CostProvider
     */
    private $costProvider;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        AwbGenerator $awbGenerator,
        ShippingAwbRepository $awbRepository,
        ClientIntermediate $clientIntermediate,
        CostProvider $costProvider,
        EntityManagerInterface $entityManager
    ) {
        $this->awbGenerator = $awbGenerator;
        $this->awbRepository = $awbRepository;
        $this->clientIntermediate = $clientIntermediate;
        $this->costProvider = $costProvider;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ExportShipmentEvent $exportShipmentEvent
     * @throws InvalidArgumentException|JsonException
     */
    public function exportShipment(ExportShipmentEvent $exportShipmentEvent): void
    {
        /** @var ShippingExportInterface $shippingExport */
        $shippingExport = $exportShipmentEvent->getShippingExport();
        $shippingGateway = $shippingExport->getShippingGateway();

        if (ShippingGatewayType::FAN_GATEWAY_CODE !== $shippingGateway->getCode()) {
            return;
        }

        /** @var ShipmentInterface $shipment */
        $shipment = $shippingExport->getShipment();
        /** @var ShippingAwb|null $shippingAwb */
        $shippingAwb = $this->awbRepository->findOneBy(['shipment' => $shipment]);
        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $shipment->getOrder()->getShippingAddress();

        $shouldGenerateNewAwb = ! ($shippingAwb && $this->checkSameAddress($shippingAwb, $shippingAddress));
        if ($shouldGenerateNewAwb && $shippingAwb) { // delete the old AWB if a new one is generated
            $this->clientIntermediate->fanClient->deleteAwb([
                'AWB' => $shippingAwb->getAwb()
            ]);
        }

        $responseData = $shouldGenerateNewAwb
            ? $this->awbGenerator->generateAwb($shipment)
            : json_decode($shippingAwb->getApiResponse(), true, 512, JSON_THROW_ON_ERROR);
        if ($responseData) {
            if (! $shippingAwb) {
                $shippingAwb = new ShippingAwb();
                $shippingAwb->setShipment($shipment);
                $shippingAwb->setApiResponse(json_encode($responseData, JSON_THROW_ON_ERROR));
                $shippingAwb->setAwb($responseData['awb']);
                $shippingAwb->setCost($this->costProvider->getVatCost($responseData['cost']));
            }
            $shippingAwb->setCountryCode($shippingAddress->getCountryCode());
            $shippingAwb->setCity($shippingAddress->getCity());
            $shippingAwb->setProvinceCode($shippingAddress->getProvinceCode());
            if (! $shippingAwb->getId()) {
                $this->entityManager->persist($shippingAwb);
            } else {
                $this->entityManager->merge($shippingAwb);
            }
            $this->entityManager->flush();

            $exportShipmentEvent->addSuccessFlash();
            $labelContent = $this->clientIntermediate->fanClient->getAwb([
                'nr' => $responseData['awb']
            ]);
            $exportShipmentEvent->saveShippingLabel($labelContent, 'html');
            $exportShipmentEvent->exportShipment();
        }
    }

    private function checkSameAddress(ShippingAwb $shippingAwb, AddressInterface $address): bool
    {
        return $address->getCountryCode() === $shippingAwb->getCountryCode() &&
            $address->getProvinceCode() === $shippingAwb->getProvinceCode() &&
            $address->getCity() === $shippingAwb->getCity();
    }
}