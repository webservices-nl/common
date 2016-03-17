<?php

namespace WebservicesNl\Common\Exception\Server;

use WebservicesNl\Common\Exception\ServerException;

/**
 * Class UnavailableException.
 */
class UnavailableException extends ServerException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'An error occurred that causes the service to be unavailable';
}
