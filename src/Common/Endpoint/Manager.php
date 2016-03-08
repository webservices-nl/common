<?php

namespace Webservicesnl\Common\Endpoint;

use Doctrine\Common\Collections\ArrayCollection;
use Webservicesnl\Common\Exception\Client\Input\InvalidException;
use Webservicesnl\Common\Exception\Client\InputException;
use Webservicesnl\Common\Exception\ClientException;
use Webservicesnl\Common\Exception\Server\NoServerAvailableException;

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
     * Try to activate an Endpoint as the active endpoint.
     * If endpoint status is Error, first check if it can be safely enabled
     *
     * @param Endpoint $newActive
     * @param bool     $force
     *
     * @throws ClientException
     * @return Endpoint
     */
    public function activateEndpoint(Endpoint $newActive, $force = false)
    {
        if (!$this->getEndpoints()->contains($newActive)) {
            throw new InputException('Endpoint is not part of this manager');
        }

        // fetch current active
        if ($force === false && $this->canBeActivated($newActive) === false) {
            throw new ClientException('Can not activate this endpoint');
        }

        // set all non-error endpoints to disabled
        $this->getEndpoints()->map(function (Endpoint $endpoint) {
            if (!$endpoint->isError()) {
                $endpoint->setStatus(Endpoint::STATUS_DISABLED);
            }
        });

        // update status of the new Endpoint to Active
        $newActive->setStatus(Endpoint::STATUS_ACTIVE);

        return $newActive;
    }

    /**
     * @param Endpoint $newActive
     *
     * @return bool
     */
    protected function canBeActivated(Endpoint $newActive)
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
     * Returns current active endpoint.
     *
     * @return Endpoint
     *
     * @throws NoServerAvailableException
     */
    public function getActiveEndpoint()
    {
        // get endpoint(s) with status active (should be one)
        $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
            return ($endpoint->getStatus() === Endpoint::STATUS_ACTIVE);
        });

        // if empty, get first endpoint not in Error
        if ($active->isEmpty() === true) {
            $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
                return ($endpoint->getStatus() === Endpoint::STATUS_DISABLED);
            });
        }

        // only error servers
        if ($active->isEmpty() === true) {
            $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
                return ($this->canBeActivated($endpoint));
            });
        }


        if ($active->isEmpty() === true) {
            throw new NoServerAvailableException('No server available');
        }

        /** @var Endpoint $endpoint */
        $endpoint = $active->first();
        $endpoint->setStatus(Endpoint::STATUS_ACTIVE);

        return $endpoint;
    }
}
