<?php

namespace WebservicesNl\Common\Exception\Client\Input;

use WebservicesNl\Common\Exception\Client\InputException;

/**
 * Class IncompleteException.
 */
class IncompleteException extends InputException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'One of the required parameters is missing or is incomplete';
}
