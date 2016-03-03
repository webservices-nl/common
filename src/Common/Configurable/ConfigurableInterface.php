<?php

namespace Webservicesnl\Common\Config;

/**
 * Interface ConfigurableInterface
 *
 * This is a generic interface for returning a declaration (config) object
 *
 * @package Webservicesnl\Common\Config
 */
interface ConfigInterface
{
    /**
     * @param array $settings
     *
     * @return mixed
     */
    public static function configure(array $settings);
}