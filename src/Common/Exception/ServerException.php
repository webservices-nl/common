<?php

namespace WebservicesNl\Common\Exception;

/**
 * Class ServerException.
 *
 * Server exception is the base class, for all server related errors
 */
class ServerException extends Exception
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'General Server error';
}
