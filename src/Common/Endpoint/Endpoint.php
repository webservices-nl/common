<?php

namespace WebservicesNl\Common\Endpoint;

use Psr\Http\Message\UriInterface;

/**
 * Class Endpoint.
 *
 * Helper class for managing a Webservices' Endpoint. It is mainly used by the EndpointManager.
 *
 * @see Manager
 *
 */
class Endpoint
{
    const STATUS_ACTIVE = 'active'; // server is active
    const STATUS_DISABLED = 'disabled'; // server is not active
    const STATUS_ERROR = 'error'; // server is not working properly

    /**
     * All statuses.
     *
     * @var array
     */
    public static $statuses = [
        self::STATUS_ACTIVE,
        self::STATUS_DISABLED,
        self::STATUS_ERROR,
    ];

    /**
     * Last successful connection attempt to server.
     *
     * @var \Datetime
     */
    protected $lastConnected;

    /**
     * Current status of the server.
     *
     * @var string
     */
    protected $status = self::STATUS_DISABLED;

    /**
     * Server url.
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * ServerConfig constructor.
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($url)
    {
        $this->uri = new Uri($url);
    }

    /**
     * @return \Datetime
     */
    public function getLastConnected()
    {
        return $this->lastConnected;
    }

    /**
     * @param \Datetime $lastConnected
     */
    public function setLastConnected(\Datetime $lastConnected)
    {
        $this->lastConnected = $lastConnected;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (in_array($status, self::$statuses, true)) {
            $this->status = $status;
        }
    }

    /**
     * The url of the endpoint
     *
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns if the endpoint is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    /**
     * Returns if the endpoint is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getStatus() === self::STATUS_DISABLED;
    }

    /**
     * Returns if the endpoint is in error
     *
     * @return bool
     */
    public function isError()
    {
        return $this->getStatus() === self::STATUS_ERROR;
    }

    /**
     * @param string $url
     *
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function createFromString($url)
    {
        return new static($url);
    }
}
