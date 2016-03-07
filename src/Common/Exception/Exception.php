<?php

namespace Webservicesnl\Common\Exception;

class Exception extends \Exception
{
    const SOAP_VERSION_V1 = 'Server';
    const SOAP_VERSION_V2 = 'Receiver';

    /**
     * @var string
     */
    public $faultCode = self::SOAP_VERSION_V1;
}
