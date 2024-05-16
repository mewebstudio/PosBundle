<?php

namespace Mews\PosBundle\Exception;

/**
 * thrown if a PHP extension is missing that is required by the gateway
 */
class MissingExtensionException extends \RuntimeException
{
    public function __construct($message = '', \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
