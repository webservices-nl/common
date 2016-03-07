<?php

namespace Webservicesnl\Common\Exception\Client;

use Webservicesnl\Common\Exception\ClientException;

/**
 * Class AuthenticationException.
 */
class AuthenticationException extends ClientException
{
    protected $errorMessage = 'Authentication of the client has failed  , the client is not logged in';
}
