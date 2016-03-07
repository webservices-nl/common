<?php

namespace Webservicesnl\Common\Exception\Client\Input;

use Webservicesnl\Common\Exception\Client\InputException;

/**
 * Class FormatIncorrectException.
 */
class FormatIncorrectException extends InputException
{
    protected $errorMessage = 'One of the parameters contains a syntax error or is in an incorrect format';
}
