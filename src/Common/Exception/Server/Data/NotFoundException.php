<?php

namespace WebservicesNl\Common\Exception\Server\Data;

use WebservicesNl\Common\Exception\Server\DataException;

/**
 * Class NotFoundException.
 */
class NotFoundException extends DataException
{
    /**
     * Error message.
     *
     * @var string
     */
    protected static $errorMessage = 'The requested data is not available';
}
