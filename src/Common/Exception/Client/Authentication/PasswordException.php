<?php

namespace Webservicesnl\Exception\Client\Authentication;

use Webservicesnl\Exception\Client\AuthenticationException;

/**
 * Class PasswordException.
 */
class PasswordException extends AuthenticationException
{
    protected $errorMessage = 'Authentication failed due to an incorrect password';
}
