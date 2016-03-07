<?php

namespace Webservicesnl\Common\Exception\Server;

use Webservicesnl\Common\Exception\ServerException;

/**
 * Class NoServerAvailableException.
 */
class NoServerAvailableException extends ServerException
{
    protected $errorMessage = 'We seem to have lost our servers';
}
