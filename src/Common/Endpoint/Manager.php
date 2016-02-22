<?php

namespace Webservicesnl\Endpoint;

use Doctrine\Common\Collections\ArrayCollection;
use Webservicesnl\Exception\Client\Input\InvalidException;
use Webservicesnl\Exception\Server\NoServerAvailableException;

/**
 * Class Manager.
 *
 * Manages Endpoints
 */
class Manager
{
    /**
     * @var ArrayCollection|Endpoint[]
     */
    protected $endpoints;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->endpoints = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * Create an endpoint.
     *
     * @param string $url
     *
     * @return Endpoint
     *
     * @throws InvalidException
     */
    public function createEndpoint($url)
    {
        $endPoint = new Endpoint($url);
        $this->addEndpoint($endPoint);

        return $endPoint;
    }

    /**
     * @param array|Endpoint[]
     *
     * @throws InvalidException
     */
    public function addEndpoints(array $endpoints = [])
    {
        foreach ($endpoints as $endpoint) {
            $this->addEndpoint($endpoint);
        }
    }

    /**
     * Add Endpoint into the pool.
     *
     * @param Endpoint $newEndpoint
     *
     * @throws InvalidException
     */
    public function addEndpoint(Endpoint $newEndpoint)
    {
        if ($this->endpoints->contains($newEndpoint)) {
            throw new InvalidException('Endpoint already added');
        }

        // all newly added Endpoints are set to DISABLED, apart from the first one
        $newEndpoint->setStatus(Endpoint::STATUS_DISABLED);
        if ($this->endpoints->isEmpty()) {
            $newEndpoint->setStatus(Endpoint::STATUS_ACTIVE);
        }

        $this->endpoints->add($newEndpoint);
    }

    /**
     * Try to activate an Endpoint.
     *
     * @param Endpoint $newActive
     * @param bool     $force
     *
     * @return Endpoint|null
     */
    public function enableEndpoint(Endpoint $newActive, $force = false)
    {
        // fetch current active
        $oldActive = $this->getActiveEndpoint();

        // set all non-error endpoints to disabled
        $this->endpoints->map(function (Endpoint $endpoint) {
            if (!$endpoint->isError()) {
                $endpoint->setStatus(Endpoint::STATUS_DISABLED);
            }
        });

        // if newActive is currently in status 'error' first determine if it can be re-enabled when force is not set
        if ($newActive->isError() === true && $force !== true) {
            $offlineInterval = new \DateTime();
            $offlineInterval->modify('-60 minutes');

            // if cool down period has expired, reset the
            if ($newActive->getLastConnected() <= $offlineInterval) {
                $newActive = $oldActive;
            }
        }

        // update status of the new Endpoint to Active
        if ($newActive instanceof Endpoint) {
            $newActive->setStatus(Endpoint::STATUS_ACTIVE);
        }

        return $newActive;
    }

    /**
     * Returns current active endpoint.
     *
     * @return Endpoint
     *
     * @throws NoServerAvailableException
     */
    public function getActiveEndpoint()
    {
        $active = null;
        $this->endpoints->map(function (Endpoint $endpoint) use (&$active) {
            if ($endpoint->getStatus() === Endpoint::STATUS_ACTIVE) {
                $active = $endpoint;
            };
        });

        if (!$active) {
            throw new NoServerAvailableException('No server available');
        }

        return $active;
    }
}
