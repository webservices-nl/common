<?php

namespace Webservicesnl\Exception\Server;

use Webservicesnl\Exception\ServerException;

/**
 * Class NoServerAvailableException.
 */
class NoServerAvailableException extends ServerException
{
    protected $errorMessage = 'We seem to have lost our servers';
}
