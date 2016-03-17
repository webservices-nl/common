<?php

namespace WebservicesNl\Common\Config;

/**
 * Interface ConfigurableInterface.
 *
 * This is a generic interface for returning a declaration (config) object
 *
 */
interface ConfigInterface
{
    /**
     * @param mixed $settings
     *
     * @return mixed
     */
    public static function configure($settings);
}
