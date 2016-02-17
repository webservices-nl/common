<?php

namespace Webservicesnl\Exception\Client;

use Webservicesnl\Exception\ClientException;

/**
 * Class AuthorizationException.
 */
class AuthorizationException extends ClientException
{
    protected $errorMessage = 'The client is authenticated, but isn’t allowed to use the requested functionality';
}
