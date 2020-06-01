<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

namespace Infifni\SyliusFanCourierPlugin\Exception;

use LogicException;

class MissingShippingGatewayException extends LogicException
{
    protected $message = 'You need to setup the FAN Courier missing gateway, it is used for shipment estimation and export.';
}