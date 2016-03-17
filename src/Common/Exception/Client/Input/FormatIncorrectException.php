<?php

namespace WebservicesNl\Common\Exception\Client\Input;

use WebservicesNl\Common\Exception\Client\InputException;

/**
 * Class FormatIncorrectException.
 */
class FormatIncorrectException extends InputException
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'One of the parameters contains a syntax error or is in an incorrect format';
}
