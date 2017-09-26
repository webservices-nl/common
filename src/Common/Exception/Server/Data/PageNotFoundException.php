<?php

namespace WebservicesNl\Common\Exception\Server\Data;

use WebservicesNl\Common\Exception\Server\DataException;

/**
 * Class PageNotFoundException.
 */
class PageNotFoundException extends DataException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'The requested result page does not exist';
}
