<?php

namespace Webservicesnl\Exception\Client\Input;

use Webservicesnl\Exception\Client\InputException;

/**
 * Class IncompleteException.
 */
class IncompleteException extends InputException
{
    protected $errorMessage = 'One of the required parameters is missing or is incomplete';
}