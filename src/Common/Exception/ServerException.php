<?php

namespace Webservicesnl\Common\Exception;

/**
 * Class ServerException.
 *
 * Server exception is the base class, for all server related errors
 */
class ServerException extends Exception
{
    /**
     * @var string
     */
    protected $errorMessage = 'Something went wrong, and it\'s our fault';
}
