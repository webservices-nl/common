<?php

namespace WebservicesNl\Common\Exception\Client;

use WebservicesNl\Common\Exception\ClientException;

/**
 * Class AuthorizationException.
 */
class AuthorizationException extends ClientException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'The client is authenticated, but isn’t allowed to use the requested functionality';
}
