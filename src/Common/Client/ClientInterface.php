<?php

namespace Webservicesnl\Common\Client;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Make a request with given client
     *
     * @return mixed
     */
    public function call();

    /**
     * Return protocol
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Return name of client
     *
     * @return string
     */
    public function getName();
}