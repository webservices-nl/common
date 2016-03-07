<?php

namespace Webservicesnl\Common\Exception\Client\Authentication;

use Webservicesnl\Exception\Client\AuthenticationException;

/**
 * Class HostRestrictionException.
 */
class HostRestrictionException extends AuthenticationException
{
    protected $errorMessage = 'Authentication failed due to restrictions on hosts and/or ip addresses';
}
