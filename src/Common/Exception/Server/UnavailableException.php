<?php

namespace Webservicesnl\Common\Exception\Server;

use Webservicesnl\Common\Exception\ServerException;

/**
 * Class UnavailableException.
 */
class UnavailableException extends ServerException
{
    protected $errorMessage = 'An error occurred that causes the service to be unavailable';
}
