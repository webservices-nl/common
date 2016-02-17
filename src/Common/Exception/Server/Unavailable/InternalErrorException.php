<?php

namespace Webservicesnl\Exception\Server\Unavailable;

use Webservicesnl\Exception\Server\UnavailableException;

/**
 * Class UnavailableInternalErrorException.
 */
class InternalErrorException extends UnavailableException
{
    protected $errorMessage = 'The service is unavailable due to an internal server error';
}
