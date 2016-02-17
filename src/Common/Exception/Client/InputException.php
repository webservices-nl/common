<?php

namespace Webservicesnl\Exception\Client;

use Webservicesnl\Exception\ClientException;

/**
 * Class ClientInputException.
 */
class InputException extends ClientException
{
    protected $errorMessage = 'An error occurred due to a problem with the client input';
}
