<?php

namespace Webservicesnl\Exception;

/**
 * Class ClientException.
 */
class ClientException extends Exception
{
    /**
     * @var string
     */
    protected $errorMessage = 'General error, caused by the client';
}
