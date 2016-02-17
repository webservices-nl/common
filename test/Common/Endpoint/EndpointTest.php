<?php

namespace Webservicesnl\Test\Endpoint;

use League\FactoryMuffin\Facade as FactoryMuffin;

use Webservicesnl\Endpoint\Endpoint;

/**
 * Class EndpointTest
 */
class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup
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
     * Test if all statuses are returned correctly
     */
    public function testInstantiation()
    {
        // create an instance with all of the possible statuses
        foreach (Endpoint::$statuses as $status) {
            /** @var Endpoint $instance */
            $instance = FactoryMuffin::instance('Webservicesnl\Endpoint\Endpoint', ['status' => $status]);

            // check if status check are valid
            $this->assertEquals($instance->isActive(), $status === Endpoint::STATUS_ACTIVE);
            $this->assertEquals($instance->isError(), $status === Endpoint::STATUS_ERROR);
            $this->assertEquals($instance->isDisabled(), $status === Endpoint::STATUS_DISABLED);

            // some other getter checks
            $this->assertEquals($instance->getStatus(), $status);
            $this->assertInstanceOf('\DateTime', $instance->getLastConnected());
            $this->assertStringStartsWith('http', $instance->getUrl());
        }
    }

    /**
     *
     */
    public function testInvalidStatus()
    {
        /** @var Endpoint $instance */
        $instance = FactoryMuffin::instance('Webservicesnl\Endpoint\Endpoint');
        $this->expectException('\InvalidArgumentException');
        $instance->setStatus('fake');
    }
}
