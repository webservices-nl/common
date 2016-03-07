<?php

namespace Webservicesnl\Test\Endpoint;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Webservicesnl\Common\Endpoint\Endpoint;
use Webservicesnl\Common\Endpoint\Manager;
use Webservicesnl\Common\Exception\Client\Input\InvalidException;

/**
 * Class ManagerTest
 * @package Webservicesnl\Test\Endpoint
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
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
     * @expectedException \Webservicesnl\Common\Exception\Server\NoServerAvailableException
     */
    public function testEmptyManager()
    {
        $manager = new Manager();
        $this->assertEmpty($manager->getEndpoints());

        $this->expectExceptionMessage('No server available');
        $manager->getActiveEndpoint();
    }

    /**
     *
     */
    public function testCreateEndpoint()
    {
        $manager = new Manager();
        $url = 'http://validurl.com';
        $newEndpoint = $manager->createEndpoint($url);

        $this->assertInstanceOf('Webservicesnl\Common\Endpoint\Endpoint', $newEndpoint);
        $this->assertEquals($url, $newEndpoint->getUrl());
        $this->assertEquals(Endpoint::STATUS_ACTIVE, $newEndpoint->getStatus());
        $this->assertEquals(null, $newEndpoint->getLastConnected());
    }

    /**
     * @throws InvalidException
     */
    public function testAddEndpointToCollection()
    {
        $manager = new Manager();

        /** @var Endpoint[] $endpoints */
        $endpoints = FactoryMuffin::seed(3, 'Webservicesnl\Common\Endpoint\Endpoint');

        foreach ($endpoints as $key => $endpoint) {
            $manager->addEndpoint($endpoint);
            $this->assertEquals($key + 1, $manager->getEndpoints()->count());
        }
    }

    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\Input\InvalidException
     * @expectedExceptionMessage Endpoint already added
     */
    public function testAddSameEndpointToCollection()
    {
        $manager = new Manager();
        /** @var Endpoint $endpoint */
        $endpoint = FactoryMuffin::instance('Webservicesnl\Common\Endpoint\Endpoint', ['url' => 'http://someurl.com']);

        $this->setExpectedExceptionFromAnnotation();
        $manager->addEndpoint($endpoint);
        $manager->addEndpoint($endpoint);
    }

    /**
     * Test if adding first and subsequent Endpoints are resp. set to Active or Disabled
     */
    public function testAddingEndpoint()
    {
        $manager = new Manager();

        /** @var Endpoint $firstEP */
        $firstEP = FactoryMuffin::instance('Webservicesnl\Common\Endpoint\Endpoint', ['status' => Endpoint::STATUS_DISABLED]);
        /** @var Endpoint $secondEP */
        $secondEP = FactoryMuffin::instance('Webservicesnl\Common\Endpoint\Endpoint', ['status' => Endpoint::STATUS_ACTIVE]);
        /** @var Endpoint $thirdEP */
        $thirdEP = FactoryMuffin::instance('Webservicesnl\Common\Endpoint\Endpoint', ['status' => Endpoint::STATUS_ERROR]);

        // add first EP, should always be set to active
        $manager->addEndpoint($firstEP);
        $this->assertEquals(
            Endpoint::STATUS_ACTIVE,
            $manager->getEndpoints()->get(0)->getStatus(),
            'First endpoint should always be active'
        );

        $manager->addEndpoint($secondEP);
        $this->assertEquals(
            Endpoint::STATUS_DISABLED,
            $manager->getEndpoints()->get(1)->getStatus(),
            'Extra endpoints are always set to disabled'
        );

        $manager->addEndpoint($thirdEP);
        $this->assertEquals(
            Endpoint::STATUS_DISABLED,
            $manager->getEndpoints()->get(2)->getStatus(),
            'Extra endpoints are always set to disabled'
        );
    }

    /**
     * Test setting multiple endpoints
     */
    public function testAddingEndpoints()
    {
        $manager = new Manager();

        $number = 3;
        $endpoints = FactoryMuffin::seed(
            $number,
            'Webservicesnl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_DISABLED]
        );

        //
        $manager->addEndpoints($endpoints);
        $this->assertCount($number, $manager->getEndpoints());
    }

    /**
     * @throws \Webservicesnl\Common\Exception\Server\NoServerAvailableException
     */
    public function testEnableEndpointInError()
    {
        $manager = new Manager();

        /** @var Endpoint $active */
        $active = FactoryMuffin::instance(
            'Webservicesnl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_ACTIVE]
        );

        /** @var Endpoint $error */
        $error = FactoryMuffin::instance(
            'Webservicesnl\Common\Endpoint\Endpoint',
            [
                'status'        => Endpoint::STATUS_ERROR,
                'lastConnected' => function () {
                    $time = new \DateTime();
                    $time->modify('-30 minutes');

                    return $time;
                },
            ]
        );

        /** @var Endpoint $error2 */
        $error2 = FactoryMuffin::instance(
            'Webservicesnl\Common\Endpoint\Endpoint',
            [
                'status'        => Endpoint::STATUS_ERROR,
                'lastConnected' => function () {
                    $time = new \DateTime();
                    $time->modify('-90 minutes');

                    return $time;

                },
            ]
        );

        $manager->getEndpoints()->add($active);
        $manager->getEndpoints()->add($error);
        $manager->getEndpoints()->add($error2);

        // try to enable endpoint which set is in Error mode
        $manager->enableEndpoint($error);
        $this->assertEquals($manager->getActiveEndpoint(), $error);

        $manager->enableEndpoint($error2, false);
        $this->assertEquals($manager->getActiveEndpoint(), $error);
    }

    /**
     * @throws InvalidException
     * @throws \Webservicesnl\Common\Exception\Server\NoServerAvailableException
     */
    public function testGetActiveEndpoint()
    {
        $amount = 3;
        $manager = new Manager();

        /** @var Endpoint[] $endpoints */
        $endpoints = FactoryMuffin::seed($amount, 'Webservicesnl\Common\Endpoint\Endpoint');
        foreach ($endpoints as $key => $endpoint) {
            $this->assertEquals(Endpoint::STATUS_DISABLED, $endpoint->getStatus());
            $manager->addEndpoint($endpoint);
        }

        $this->assertEquals($amount, $manager->getEndpoints()->count(), 'number of Endpoints should match');
        $this->assertEquals($manager->getActiveEndpoint(), $manager->getEndpoints()->first());

        $manager->enableEndpoint($endpoints[2]);
        $this->assertEquals($manager->getActiveEndpoint(), $manager->getEndpoints()->last());
    }
}
