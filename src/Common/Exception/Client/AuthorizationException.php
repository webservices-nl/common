<?php

namespace Webservicesnl\Common\Exception\Client;

use Webservicesnl\Common\Exception\ClientException;

/**
 * Class AuthorizationException.
 */
class AuthorizationException extends ClientException
{
    protected $errorMessage = 'The client is authenticated, but isn’t allowed to use the requested functionality';
}
