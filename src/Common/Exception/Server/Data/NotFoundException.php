<?php

namespace Webservicesnl\Common\Exception\Server\Data;

use Webservicesnl\Common\Exception\Server\DataException;

/**
 * Class NotFoundException.
 */
class NotFoundException extends DataException
{
    protected $errorMessage = 'The requested data is not available';
}
