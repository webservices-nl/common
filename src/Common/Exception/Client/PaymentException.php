<?php

namespace Webservicesnl\Common\Exception\Client;

use Webservicesnl\Common\Exception\ClientException;

/**
 * Class ClientPaymentException.
 */
class PaymentException extends ClientException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'The request cannot be processed, due to sufficient balance/credits';
}
