<?php

namespace WebservicesNl\Common\Exception\Server\Unavailable;

use WebservicesNl\Common\Exception\Server\UnavailableException;

/**
 * Class TemporaryException.
 */
class TemporaryException extends UnavailableException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'The service is unavailable due to a temporary technical problem';
}
