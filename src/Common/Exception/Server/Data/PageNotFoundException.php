<?php

namespace Webservicesnl\Exception\Server\Data;

use Webservicesnl\Exception\Server\DataException;

/**
 * Class PageNotFoundException
 *
 * @package Webservicesnl\Exception\Server\Data
 */
class PageNotFoundException extends DataException
{
    protected $errorMessage = 'The requested result page does not exist';
}
