<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Shipping\Calculator;

use Infifni\SyliusFanCourierPlugin\Form\Type\ShippingGatewayType;
use Infifni\SyliusFanCourierPlugin\Shipping\CostProvider;
use Psr\Cache\InvalidArgumentException;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class FanCalculator implements CalculatorInterface
{
    /**
     * @var CostProvider
     */
    private $costProvider;

    /**
     * @param CostProvider $costProvider
     */
    public function __construct(CostProvider $costProvider)
    {
        $this->costProvider = $costProvider;
    }

    /**
     * @param Shipment|ShipmentInterface $subject
     * @param array $configuration
     * @return int
     * @throws InvalidArgumentException
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        return $this->costProvider->getCost($subject);
    }

    public function getType(): string
    {
        return ShippingGatewayType::FAN_GATEWAY_CODE;
    }
}