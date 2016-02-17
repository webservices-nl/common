<?php

namespace Webservicesnl\Exception\Server\Unavailable;

use Webservicesnl\Exception\Server\UnavailableException;

/**
 * Class TemporaryException.
 */
class TemporaryException extends UnavailableException
{
    protected $errorMessage = 'The service is unavailable due to a temporary technical problem';
}
