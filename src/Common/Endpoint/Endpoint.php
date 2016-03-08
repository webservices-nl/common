<?php

namespace Webservicesnl\Common\Endpoint;

use Webservicesnl\Common\Exception\Client\InputException;

/**
 * Class Endpoint.
 *
 * Class definition of Webservices Endpoint
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
    protected $lastConnected = null;

    /**
     * Current status of the server.
     *
     * @var string
     */
    protected $status = self::STATUS_DISABLED;

    /**
     * Server url.
     *
     * @var string
     */
    protected $url;

    /**
     * ServerConfig constructor.
     *
     * @param string $url
     *
     * @throws InputException
     */
    public function __construct($url)
    {
        $this->url = $url;
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
        if (!in_array($status, self::$statuses)) {
            throw new \InvalidArgumentException('Not a valid status');
        }
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getStatus() === self::STATUS_DISABLED;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->getStatus() === self::STATUS_ERROR;
    }
}
