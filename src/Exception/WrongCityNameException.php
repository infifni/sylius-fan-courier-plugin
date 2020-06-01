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
use Throwable;

class WrongCityNameException extends LogicException
{
    public function __construct($cityName = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct("City $cityName is wrong on FAN Courier API side.", $code, $previous);
    }
}