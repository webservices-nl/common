<?php

namespace Webservicesnl\Common\Exception\Client\Authentication;

use Webservicesnl\Common\Exception\Client\AuthenticationException;

/**
 * Class PasswordException.
 */
class PasswordException extends AuthenticationException
{
    protected $errorMessage = 'Authentication failed due to an incorrect password';
}
