<?php

namespace Webservicesnl\Common\Exception\Server\Unavailable;

use Webservicesnl\Common\Exception\Server\UnavailableException;

/**
 * Class UnavailableInternalErrorException.
 */
class InternalErrorException extends UnavailableException
{
    protected $errorMessage = 'The service is unavailable due to an internal server error';
}
