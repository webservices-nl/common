<?php

namespace Webservicesnl\Common\Exception\Server\Data;

use Webservicesnl\Common\Exception\Server\DataException;

/**
 * Class PageNotFoundException.
 *
 */
class PageNotFoundException extends DataException
{
    protected $errorMessage = 'The requested result page does not exist';
}
