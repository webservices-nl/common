<?php

namespace Webservicesnl\Exception\Server\Data;

use Webservicesnl\Exception\Server\DataException;

/**
 * Class NotFoundException.
 */
class NotFoundException extends DataException
{
    protected $errorMessage = 'The requested data is not available';
}
