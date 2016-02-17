<?php

namespace Webservicesnl\Exception\Server;

use Webservicesnl\Exception\ServerException;

/**
 * Class DataException.
 */
class DataException extends ServerException
{
    protected $errorMessage = 'An error occurred while retrieving requested data';
}
