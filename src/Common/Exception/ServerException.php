<?php

namespace Webservicesnl\Common\Exception;

/**
 * Class ServerException.
 */
class ServerException extends Exception
{
    /**
     * @var string
     */
    protected $errorMessage = 'Something went wrong, and it\'s our fault';
}
