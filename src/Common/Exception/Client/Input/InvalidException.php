<?php

namespace WebservicesNl\Common\Exception\Client\Input;

use WebservicesNl\Common\Exception\Client\InputException;

/**
 * Class InvalidException.
 */
class InvalidException extends InputException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'One of the parameters contains an invalid or disallowed value';
}
