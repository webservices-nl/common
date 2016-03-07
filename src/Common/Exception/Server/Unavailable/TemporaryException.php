<?php

namespace Webservicesnl\Common\Exception\Server\Unavailable;

use Webservicesnl\Common\Exception\Server\UnavailableException;

/**
 * Class TemporaryException.
 */
class TemporaryException extends UnavailableException
{
    protected $errorMessage = 'The service is unavailable due to a temporary technical problem';
}
