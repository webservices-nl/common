<?php

namespace WebservicesNl\Common\Exception\Server;

use WebservicesNl\Common\Exception\ServerException;

/**
 * Class NoServerAvailableException.
 */
class NoServerAvailableException extends ServerException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'We seem to have lost our servers';
}
