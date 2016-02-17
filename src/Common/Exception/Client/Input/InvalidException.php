<?php

namespace Webservicesnl\Exception\Client\Input;

use Webservicesnl\Exception\Client\InputException;

/**
 * Class InvalidException.
 */
class InvalidException extends InputException
{
    protected $errorMessage = 'One of the parameters contains an invalid or disallowed value';
}
