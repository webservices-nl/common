<?php

namespace Webservicesnl\Common\Exception\Client;

use Webservicesnl\Common\Exception\ClientException;

/**
 * Class ClientInputException.
 */
class InputException extends ClientException
{
    protected $errorMessage = 'An error occurred due to a problem with the client input';
}
