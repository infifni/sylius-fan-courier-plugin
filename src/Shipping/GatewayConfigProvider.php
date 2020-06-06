<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Shipping;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingGatewayRepositoryInterface;
use Infifni\SyliusFanCourierPlugin\Exception\MissingShippingGatewayException;
use Infifni\SyliusFanCourierPlugin\Form\Type\ShippingGatewayType;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class GatewayConfigProvider
{
    public const GATEWAY_CACHE_KEY = 'fan_gateway_object';

    /**
     * @var array [
     *      'client_id' => 'string',
     *      'username' => 'string',
     *      'password' => 'string,
     *      'parcels_or_envelopes' => 'value can be colete or plicuri',
     *      'number_of_packages' => 'integer',
     *      'who_pays_awb' => 'value can be destinatar or expeditor',
     *      'no_vat' => true || false,
     *      'hide_shipping_cost' => true || false,
     *      'free_shipping_min_value' => 'integer or float with 2 decimals',
     *      'fixed_cost' => 'integer or float with 2 decimals',
     *      'active_service' => 'value can be one of Infifni\FanCourierApiClient\Request\EndpointInterface::SERVICE_ALLOWED_VALUES',
     *      'with_repayment' => true || false,
     *      'who_pays_repayment' => 'value can be destinatar or expeditor',
     *      'add_shipping_cost_to_repayment' => true || false,
     *      'who_pays_repayment' => 'value can be destinatar or expeditor',
     *      'with_assurance' => true || false,
     *      'with_product_codes_in_content' => true || false,
     *      'observations' => 'string',
     *      'sender_contact_person' => 'string',
     *      'open_allowed' => true || false,
     *      'epod' => true || false
     * ]
     */
    private $shippingGatewayConfig;

    /**
     * GatewayConfigProvider constructor.
     * @param AdapterInterface $cache
     * @param ShippingGatewayRepositoryInterface $gatewayRepository
     * @throws InvalidArgumentException
     * @throws MissingShippingGatewayException
     */
    public function __construct(AdapterInterface $cache, ShippingGatewayRepositoryInterface $gatewayRepository)
    {
        $item = $cache->getItem(self::GATEWAY_CACHE_KEY);
        if (! $item->isHit()) {
            /** @var ShippingGatewayInterface $shippingGateway */
            $shippingGateway = $gatewayRepository->findOneByCode(ShippingGatewayType::FAN_GATEWAY_CODE);
            if (! $shippingGateway) {
                throw new MissingShippingGatewayException();
            }

            $item->set($shippingGateway);
            $cache->save($item);
        }
        $this->shippingGatewayConfig = $item->get()->getConfig();
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->shippingGatewayConfig;
    }
}