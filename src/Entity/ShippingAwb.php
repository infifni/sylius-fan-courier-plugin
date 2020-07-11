<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

declare(strict_types=1);

namespace Infifni\SyliusFanCourierPlugin\Entity;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class ShippingAwb implements ResourceInterface
{
    /** @var int|null */
    protected $id;

    /** @var ShipmentInterface|null */
    protected $shipment;

    /** @var string|null */
    protected $awb;

    /** @var string|null */
    protected $apiResponse;

    /** @var float|null */
    protected $cost;

    /** @var string|null */
    protected $countryCode;

    /** @var string|null */
    protected $provinceCode;

    /** @var string|null */
    protected $city;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ShipmentInterface|null
     */
    public function getShipment(): ?ShipmentInterface
    {
        return $this->shipment;
    }

    /**
     * @param ShipmentInterface|null $shipment
     */
    public function setShipment(?ShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    /**
     * @return string|null
     */
    public function getAwb(): ?string
    {
        return $this->awb;
    }

    /**
     * @param string|null $awb
     */
    public function setAwb(?string $awb): void
    {
        $this->awb = $awb;
    }

    /**
     * @return string|null
     */
    public function getApiResponse(): ?string
    {
        return $this->apiResponse;
    }

    /**
     * @param string|null $apiResponse
     */
    public function setApiResponse(?string $apiResponse): void
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * @return float|null
     */
    public function getCost(): ?float
    {
        return $this->cost;
    }

    /**
     * @param float|null $cost
     */
    public function setCost(?float $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string|null
     */
    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    /**
     * @param string|null $provinceCode
     */
    public function setProvinceCode(?string $provinceCode): void
    {
        $this->provinceCode = $provinceCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }
}
