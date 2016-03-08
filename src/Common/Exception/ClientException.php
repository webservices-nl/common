<?php

namespace Webservicesnl\Common\Exception;

/**
 * Class ClientException.
 *
 * Client Exception is the base exception for all client created errors.
 */
class ClientException extends Exception
{
    /**
     * @var string
     */
    protected $errorMessage = 'General error, caused by the client';
}
