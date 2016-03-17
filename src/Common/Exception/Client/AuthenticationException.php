<?php

namespace WebservicesNl\Common\Exception\Client;

use WebservicesNl\Common\Exception\ClientException;

/**
 * Class AuthenticationException.
 */
class AuthenticationException extends ClientException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'Authentication of the client has failed, the client is not logged in';
}
