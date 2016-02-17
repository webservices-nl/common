<?php

namespace Webservicesnl\Endpoint;

use Webservicesnl\Exception\Client\InputException;

/**
 * Class Endpoint.
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
        // check if url is a valid Url
        // _^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS
//        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
//            throw new InputException('Not a valid URL');
//        }

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
