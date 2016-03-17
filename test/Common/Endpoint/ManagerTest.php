<?php

namespace WebservicesNl\Test\Common\Endpoint;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Endpoint\Endpoint;
use WebservicesNl\Common\Endpoint\Manager;

/**
 * Class ManagerTest.
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup.
     */
    public static function setupBeforeClass()
    {
        FactoryMuffin::setCustomSaver(function () {
            return true;
        });
        FactoryMuffin::setCustomSetter(function ($object, $name, $value) {
            $name = 'set' . ucfirst(strtolower($name));
            if (method_exists($object, $name)) {
                $object->{$name}($value);
            }
        });
        FactoryMuffin::loadFactories(dirname(__DIR__) . '/Factories');
        FactoryMuffin::setCustomMaker(function ($class) {
            $faker = FactoryMuffin::getFaker();

            return new $class($faker->url);
        });
    }

    /**
     * @expectedExceptionMessage No active server available
     * @expectedException \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testEmptyManager()
    {
        $manager = new Manager();
        static::assertEmpty($manager->getEndpoints());
        $manager->getActiveEndpoint();
    }

    /**
     * Test to create and endpoint
     */
    public function testCreateEndpoint()
    {
        $manager = new Manager();
        $url = 'http://username:fakepassword@validurl.com/bla';
        $newEndpoint = $manager->createEndpoint($url);

        static::assertInstanceOf('WebservicesNl\Common\Endpoint\Endpoint', $newEndpoint);
        static::assertEquals($url, (string)$newEndpoint->getUri());
        static::assertEquals(Endpoint::STATUS_ACTIVE, $newEndpoint->getStatus());
        static::assertEquals(null, $newEndpoint->getLastConnected());
    }

    /**
     * @throws InputException
     */
    public function testAddEndpointToCollection()
    {
        $manager = new Manager();

        /** @var Endpoint[] $endpoints */
        $endpoints = FactoryMuffin::seed(3, 'WebservicesNl\Common\Endpoint\Endpoint');

        foreach ($endpoints as $key => $endpoint) {
            $manager->addEndpoint($endpoint);
            static::assertEquals($key + 1, $manager->getEndpoints()->count());
        }
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Endpoint already added
     */
    public function testAddSameEndpointToCollection()
    {
        $manager = new Manager();
        /** @var Endpoint $endpoint */
        $endpoint = FactoryMuffin::instance('WebservicesNl\Common\Endpoint\Endpoint', ['url' => 'http://someurl.com']);

        $manager->addEndpoint($endpoint);
        $manager->addEndpoint($endpoint);
    }

    /**
     * Test if adding first and subsequent Endpoints are resp. set to Active or Disabled.
     */
    public function testAddingEndpoint()
    {
        $manager = new Manager();

        /** @var Endpoint $firstEP */
        $firstEP = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_DISABLED]
        );
        /** @var Endpoint $secondEP */
        $secondEP = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_ACTIVE]
        );

        /** @var Endpoint $thirdEP */
        $thirdEP = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_ERROR]
        );

        // add first EP, should always be set to active
        $manager->addEndpoint($firstEP);
        static::assertEquals(
            Endpoint::STATUS_ACTIVE,
            $manager->getEndpoints()->get(0)->getStatus(),
            'First endpoint should always be active'
        );

        $manager->addEndpoint($secondEP);
        static::assertEquals(
            Endpoint::STATUS_DISABLED,
            $manager->getEndpoints()->get(1)->getStatus(),
            'Extra endpoints are always set to disabled'
        );

        $manager->addEndpoint($thirdEP);
        static::assertEquals(
            Endpoint::STATUS_DISABLED,
            $manager->getEndpoints()->get(2)->getStatus(),
            'Extra endpoints are always set to disabled'
        );
    }

    /**
     * Test setting multiple endpoints.
     */
    public function testAddingEndpoints()
    {
        $manager = new Manager();

        $number = 3;
        $endpoints = FactoryMuffin::seed(
            $number,
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_DISABLED]
        );

        // make sure that all these endpoints are set to disabled...
        foreach ($endpoints as $endpoint) {
            $manager->getEndpoints()->add($endpoint);
        }

        static::assertCount($number, $manager->getEndpoints());
        static::assertEquals(Endpoint::STATUS_ACTIVE, $manager->getActiveEndpoint()->getStatus());
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Can not activate this endpoint
     */
    public function testEnableEndpointInError()
    {
        $manager = new Manager();

        /** @var Endpoint $active */
        $active = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_ACTIVE]
        );

        /** @var Endpoint $shortTimeout */
        $shortTimeout = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            [
                'status'        => Endpoint::STATUS_ERROR,
                'lastConnected' => function () {
                    $time = new \DateTime();
                    $time->modify('-30 minutes');

                    return $time;
                },
            ]
        );

        $manager->getEndpoints()->add($active);
        $manager->getEndpoints()->add($shortTimeout);

        // try to enable endpoint in Error with a short time out
        $manager->activateEndpoint($shortTimeout);
        static::assertEquals($manager->getActiveEndpoint(), $active);
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testEnableErrorEndpoints()
    {
        $manager = new Manager();
        $amount = 4;

        /** @var Endpoint[] $disabled */
        $endpoints = FactoryMuffin::seed(
            $amount,
            'WebservicesNl\Common\Endpoint\Endpoint',
            [
                'status' => Endpoint::STATUS_ERROR,
                'lastConnected' => function () {
                    $time = new \DateTime();
                    $time->modify('-90 minutes');

                    return $time;
                },
            ]
        );
        foreach ($endpoints as $endpoint) {
            $manager->getEndpoints()->add($endpoint);
        }

        $newActive = $manager->getActiveEndpoint();
        static::assertEquals($newActive->getStatus(), Endpoint::STATUS_ACTIVE);
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testGetActiveEndpoint()
    {
        $amount = 3;
        $manager = new Manager();

        /** @var Endpoint[] $endpoints */
        $endpoints = FactoryMuffin::seed($amount, 'WebservicesNl\Common\Endpoint\Endpoint');
        foreach ($endpoints as $key => $endpoint) {
            static::assertEquals(Endpoint::STATUS_DISABLED, $endpoint->getStatus()); // by default status should disabled
            $manager->addEndpoint($endpoint);
        }

        static::assertEquals($amount, $manager->getEndpoints()->count(), 'number of Endpoints should match');
        static::assertEquals($manager->getActiveEndpoint(), $manager->getEndpoints()->first());

        $manager->activateEndpoint($endpoints[2]);
        static::assertEquals($manager->getActiveEndpoint(), $manager->getEndpoints()->last());
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Endpoint is not part of this manager
     */
    public function testInvalidEndpoint()
    {
        $amount = 3;
        $manager = new Manager();

        /** @var Endpoint[] $endpoints */
        $endpoints = FactoryMuffin::seed($amount, 'WebservicesNl\Common\Endpoint\Endpoint');
        foreach ($endpoints as $endpoint) {
            $manager->addEndpoint($endpoint);
        }

        /** @var Endpoint $fakeEndpoint */
        $fakeEndpoint = FactoryMuffin::instance('WebservicesNl\Common\Endpoint\Endpoint');

        $manager->activateEndpoint($fakeEndpoint);
    }


    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testEnableEndpointInErrorWithForce()
    {
        $manager = new Manager();

        /** @var Endpoint $active */
        $active = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_ACTIVE]
        );

        /** @var Endpoint $shortTimeout */
        $shortTimeout = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            [
                'status' => Endpoint::STATUS_ERROR,
                'lastConnected' => function () {
                    $time = new \DateTime();
                    $time->modify('-30 minutes');

                    return $time;
                },
            ]
        );

        $manager->addEndpoint($active);
        $manager->addEndpoint($shortTimeout);

        // try to enable endpoint in Error with a short time out
        $result = $manager->activateEndpoint($shortTimeout, true);

        static::assertEquals($manager->getActiveEndpoint(), $result);
    }
}
