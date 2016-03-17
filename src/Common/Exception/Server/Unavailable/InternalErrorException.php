<?php

namespace WebservicesNl\Common\Exception\Server\Unavailable;

use WebservicesNl\Common\Exception\Server\UnavailableException;

/**
 * Class UnavailableInternalErrorException.
 */
class InternalErrorException extends UnavailableException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'The service is unavailable due to an internal server error';
}
