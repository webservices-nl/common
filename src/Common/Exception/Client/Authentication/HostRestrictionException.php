<?php

namespace WebservicesNl\Common\Exception\Client\Authentication;

use WebservicesNl\Common\Exception\Client\AuthenticationException;

/**
 * Class HostRestrictionException.
 */
class HostRestrictionException extends AuthenticationException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'Authentication failed due to restrictions on hosts and/or ip addresses';
}
