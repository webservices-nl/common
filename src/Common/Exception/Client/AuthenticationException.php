<?php

namespace Webservicesnl\Exception\Client;

use Webservicesnl\Exception\ClientException;

/**
 * Class AuthenticationException.
 */
class AuthenticationException extends ClientException
{
    protected $errorMessage = 'Authentication of the client has failed  , the client is not logged in';
}
