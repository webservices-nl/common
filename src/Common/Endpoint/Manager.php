<?php

namespace Webservicesnl\Common\Endpoint;

use Doctrine\Common\Collections\ArrayCollection;
use Webservicesnl\Common\Exception\Client\Input\InvalidException;
use Webservicesnl\Common\Exception\Server\NoServerAvailableException;

/**
 * Class Manager.
 *
 * Manages Endpoints for a protocol client
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
     * Create endpoint.
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
        $status = ($this->endpoints->isEmpty()) ? Endpoint::STATUS_ACTIVE : Endpoint::STATUS_DISABLED;
        $newEndpoint->setStatus($status);

        $this->getEndpoints()->add($newEndpoint);
    }

    /**
     * @param array|Endpoint[] $endpoints
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
     * Try to activate an Endpoint as the active endpoint.
     * If endpoint status is Error, first check if it can be safely enabled
     *
     * @param Endpoint $newActive
     * @param bool     $force
     *
     * @throws InvalidException
     * @return Endpoint
     */
    public function activateEndpoint(Endpoint $newActive, $force = false)
    {
        if (!$this->getEndpoints()->contains($newActive)) {
            throw new InvalidException('Endpoint is not part of this manager');
        }


        if ($force === false && $this->canBeActivated($newActive) === false) {
            throw new InvalidException('Can not activate this endpoint');
        }

        // set all non-error endpoints to disabled
        $this->getEndpoints()->map(function (Endpoint $endpoint) {
            if ($endpoint->isError() === false) {
                $endpoint->setStatus(Endpoint::STATUS_DISABLED);
            }
        });

        // update status of the new Endpoint to Active
        $newActive->setStatus(Endpoint::STATUS_ACTIVE);

        return $newActive;
    }

    /**
     * @return ArrayCollection
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * Try to determine if this endpoint should re-enabled
     *
     * @param Endpoint $newActive
     *
     * @return bool
     */
    private function canBeActivated(Endpoint $newActive)
    {
        // if newActive is currently in status 'error' first determine if it can be re-enabled when force is not set
        if ($newActive->isError() === true) {
            $offlineInterval = new \DateTime();
            $offlineInterval->modify('-60 minutes');

            return ($newActive->getLastConnected() <= $offlineInterval);
        }

        return true;
    }

    /**
     * Returns current an active endpoint.
     *
     * @return Endpoint
     *
     * @throws NoServerAvailableException
     */
    public function getActiveEndpoint()
    {
        // try to get endpoint with status active (should be one)
        $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
            return ($endpoint->getStatus() === Endpoint::STATUS_ACTIVE);
        });

        // when active is empty, get first disabled endpoint
        if ($active->isEmpty() === true) {
            $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
                return ($endpoint->getStatus() === Endpoint::STATUS_DISABLED);
            });
        }

        // when still empty, get first endpoint in Error
        if ($active->isEmpty() === true) {
            $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
                return $this->canBeActivated($endpoint);
            });
        }

        if ($active->isEmpty() === true) {
            throw new NoServerAvailableException('No active server available');
        }

        /** @var Endpoint $endpoint */
        $endpoint = $active->first();
        $endpoint->setStatus(Endpoint::STATUS_ACTIVE);

        return $endpoint;
    }
}
