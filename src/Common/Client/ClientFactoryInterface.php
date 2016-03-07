<?php

namespace Webservicesnl\Common\Client;

/**
 * Interface ClientFactoryInterface.
 */
interface ClientFactoryInterface
{
    /**
     * @param string $platform
     *
     * @return static
     */
    public function build($platform);

    /**
     * Build connector.
     *
     * @param array $settings
     *
     * @return ClientInterface
     */
    public function create(array $settings = []);
}