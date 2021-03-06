<?php

namespace WebservicesNl\Common\Client;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Returns the protocol name.
     *
     * @return string
     */
    public function getProtocolName();

    /**
     * Make a request.
     */
    public function call();
}
