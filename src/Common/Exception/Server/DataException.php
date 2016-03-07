<?php

namespace Webservicesnl\Common\Exception\Server;

use Webservicesnl\Common\Exception\ServerException;

/**
 * Class DataException.
 */
class DataException extends ServerException
{
    protected $errorMessage = 'An error occurred while retrieving requested data';
}
