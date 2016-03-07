<?php

namespace Webservicesnl\Common\Client;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface ClientFactoryInterface.
 */
interface ClientFactoryInterface extends LoggerAwareInterface
{
    /**
     * @param string          $platform
     * @param LoggerInterface $logger
     *
     * @return static
     */
    public static function build($platform, LoggerInterface $logger = null);

    /**
     * Build connector.
     *
     * @param array $settings
     *
     * @return mixed
     */
    public function create(array $settings = []);
}
