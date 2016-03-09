<?php

namespace Webservicesnl\Common\Exception\Client\Authentication;

use Webservicesnl\Common\Exception\Client\AuthenticationException;

/**
 * Class UsernameException.
 */
class UsernameException extends AuthenticationException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'Authentication failed due to an invalid username';
}
