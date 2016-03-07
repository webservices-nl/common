<?php

namespace Webservicesnl\Common\Exception\Client\Authentication;

use Webservicesnl\Exception\Client\AuthenticationException;

/**
 * Class UsernameException.
 */
class UsernameException extends AuthenticationException
{
    protected $errorMessage = 'Authentication failed due to an invalid username';
}
