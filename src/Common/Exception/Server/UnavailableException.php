<?php

namespace Webservicesnl\Exception\Server;

use Webservicesnl\Exception\ServerException;

/**
 * Class UnavailableException.
 */
class UnavailableException extends ServerException
{
    protected $errorMessage = 'An error occurred that causes the service to be unavailable';
}
