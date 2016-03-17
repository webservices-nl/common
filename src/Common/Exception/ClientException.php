<?php

namespace WebservicesNl\Common\Exception;

/**
 * Class ClientException.
 *
 * Client Exception is the base exception for all client created errors.
 */
class ClientException extends Exception
{
    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage = 'General error, caused by the client';
}
