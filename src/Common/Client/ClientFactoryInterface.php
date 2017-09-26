<?php

namespace WebservicesNl\Common\Client;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface ClientFactoryInterface.
 *
 * Contract for ClientFactories used by the platform generator
 */
interface ClientFactoryInterface extends LoggerAwareInterface
{
    /**
     * Initiate ClientFactory.
     *
     * @param string          $platform
     * @param LoggerInterface $logger
     *
     * @return static
     */
    public static function build($platform, LoggerInterface $logger = null);

    /**
     * Build this Factory providing connector.
     *
     * @param array $settings
     *
     * @return mixed
     */
    public function create(array $settings = []);
}
