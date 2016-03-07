<?php

namespace Webservicesnl\Common\Exception\Client\Authentication;

use Webservicesnl\Common\Exception\Client\AuthenticationException;

/**
 * Class HostRestrictionException.
 */
class HostRestrictionException extends AuthenticationException
{
    protected $errorMessage = 'Authentication failed due to restrictions on hosts and/or ip addresses';
}
