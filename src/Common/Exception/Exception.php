<?php

namespace WebservicesNl\Common\Exception;

/**
 * Class Exception.
 *
 * This is the default Webservices Exception which extends the default exception
 * All other Webservices exceptions extends this class. Try catch this exception for catching all Webservices errors
 */
class Exception extends \Exception
{
    const SOAP_VERSION_V1 = 'Server';
    const SOAP_VERSION_V2 = 'Receiver';

    /**
     * @var string
     */
    public $faultCode = self::SOAP_VERSION_V1;
}
