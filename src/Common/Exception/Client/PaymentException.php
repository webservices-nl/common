<?php

namespace Webservicesnl\Exception\Client;

use Webservicesnl\Exception\ClientException;

/**
 * Class ClientPaymentException.
 */
class PaymentException extends ClientException
{
    protected $errorMessage = 'The request cannot be processed, due to sufficient balance/credits';
}
