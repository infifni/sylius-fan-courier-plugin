<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Api;

use Infifni\FanCourierApiClient\Client;
use Infifni\SyliusFanCourierPlugin\Shipping\GatewayConfigProvider;

class ClientIntermediate
{
    /**
     * @var Client
     */
    public $fanClient;

    public function __construct(GatewayConfigProvider $gatewayConfigProvider)
    {
        $this->fanClient = new Client(
            $gatewayConfigProvider->get()['client_id'],
            $gatewayConfigProvider->get()['username'],
            $gatewayConfigProvider->get()['password']
        );
    }
}