<?php

namespace WebservicesNl\Common\Endpoint;

use Doctrine\Common\Collections\ArrayCollection;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Common\Exception\Server\NoServerAvailableException;

/**
 * Class Manager.
 *
 * Manages Endpoints for a protocol client. The manager keeps track of the activated endpoint. It can provide another
 * endpoint, or enable or disable one. If an endpoint is in Error, it can be enabled after 60 min.
 *
 * @see Endpoint
 */
class Manager
{
    /**
     * @var ArrayCollection|Endpoint[]
     */
    protected $endpoints;

    /**
     * Manager constructor.
     *
     * Initiate empty endpoint collection
     */
    public function __construct()
    {
        $this->endpoints = new ArrayCollection();
    }

    /**
     * Create endpoint.
     * Create endpoint from url and add to collection.
     *
     * @param string $url
     *
     * @return Endpoint
     *
     * @throws InputException
     * @throws \InvalidArgumentException
     */
    public function createEndpoint($url)
    {
        $endPoint = new Endpoint($url);
        $this->addEndpoint($endPoint);

        return $endPoint;
    }

    /**
     * Checks if the given uri is already added to collection.
     *
     * @param string $uri
     *
     * @return bool
     */
    public function hasEndpoint($uri)
    {
        /** @var ArrayCollection $urlsFound */
        $urlsFound = $this->getEndpoints()->filter(function (Endpoint $endpoint) use ($uri) {
            // return when URI and URI string are equal
            return (string) $endpoint->getUri() === $uri;
        });

        return $urlsFound->isEmpty() === false;
    }

    /**
     * Add Endpoint into the pool.
     *
     * @param Endpoint $newEndpoint
     *
     * @throws InputException
     */
    public function addEndpoint(Endpoint $newEndpoint)
    {
        if ($this->hasEndpoint((string) $newEndpoint->getUri())) {
            throw new InputException('Endpoint already added');
        }

        // all newly added Endpoints are set to DISABLED, apart from the first one
        $status = $this->getEndpoints()->isEmpty() ? Endpoint::STATUS_ACTIVE : Endpoint::STATUS_DISABLED;
        $newEndpoint->setStatus($status);

        $this->getEndpoints()->add($newEndpoint);
    }

    /**
     * return Endpoint collection.
     *
     * @return ArrayCollection
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * Try to activate an Endpoint as the active endpoint.
     * If endpoint status is Error, first check if it can be safely enabled.
     *
     * @param Endpoint $newActive Endpoint to be enabled
     * @param bool     $force     when true, skips the cool down check
     *
     * @throws InputException
     *
     * @return Endpoint
     */
    public function activateEndpoint(Endpoint $newActive, $force = false)
    {
        if (!$this->getEndpoints()->contains($newActive)) {
            throw new InputException('Endpoint is not part of this manager');
        }

        if ($force === false && $this->canBeActivated($newActive) === false) {
            throw new InputException('Can not activate this endpoint');
        }

        $this->disableAll();
        $newActive->setStatus(Endpoint::STATUS_ACTIVE);

        return $newActive;
    }

    /**
     * Determine if this endpoint can re-enabled.
     *
     * @param Endpoint $newActive
     *
     * @return bool
     */
    private function canBeActivated(Endpoint $newActive)
    {
        // if newActive is currently in error, see if it can be re-enabled
        if ($newActive->isError() === true) {
            $offlineInterval = new \DateTime();
            $offlineInterval->modify('-60 minutes');

            return $newActive->getLastConnected() !== null && $newActive->getLastConnected() <= $offlineInterval;
        }

        return true;
    }

    /**
     * Disable all endpoints, except the ones in error.
     *
     * @throws InputException
     */
    private function disableAll()
    {
        // set all non-error endpoints to disabled
        $this->getEndpoints()->map(function (Endpoint $endpoint) {
            if ($endpoint->isError() === false) {
                $endpoint->setStatus(Endpoint::STATUS_DISABLED);
            }
        });
    }

    /**
     * Returns a active endpoint.
     * Tries to find the current active endpoint, or enable one.
     *
     * @return Endpoint
     *
     * @throws NoServerAvailableException
     */
    public function getActiveEndpoint()
    {
        // try to get endpoint with status active
        $active = $this->getEndpoints()->filter(function (Endpoint $endpoint) {
            return $endpoint->isActive();
        });

        // when empty, try other endpoints
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

    /**
     * Update active endPoint
     *
     * @throws NoServerAvailableException
     */
    public function updateLastConnected()
    {
        $endpoint = $this->getActiveEndpoint();
        $endpoint->setLastConnected(new \DateTime());

        return $endpoint;
    }
}
