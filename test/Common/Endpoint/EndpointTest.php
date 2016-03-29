<?php

namespace WebservicesNl\Test\Common\Endpoint;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Endpoint\Endpoint;

/**
 * Class EndpointTest.
 */
class EndpointTest extends \PHPUnit_Framework_TestCase
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
     * Test if all statuses are returned correctly.
     */
    public function testInstantiation()
    {
        // create an instance with all of the possible statuses
        foreach (Endpoint::$statuses as $status) {
            /** @var Endpoint $instance */
            $instance = FactoryMuffin::instance('WebservicesNl\Common\Endpoint\Endpoint', ['status' => $status]);

            // check if status check are valid
            static::assertEquals($instance->isActive(), $status === Endpoint::STATUS_ACTIVE);
            static::assertEquals($instance->isError(), $status === Endpoint::STATUS_ERROR);
            static::assertEquals($instance->isDisabled(), $status === Endpoint::STATUS_DISABLED);

            // some other getter checks
            static::assertEquals($instance->getStatus(), $status);
            static::assertInstanceOf('\DateTime', $instance->getLastConnected());
            static::assertInstanceOf('WebservicesNl\Common\Endpoint\Uri', $instance->getUri());
        }
    }

    /**
     * test status can't be set to something weird.
     */
    public function testInvalidStatus()
    {
        /** @var Endpoint $instance */
        $instance = FactoryMuffin::instance(
            'WebservicesNl\Common\Endpoint\Endpoint',
            ['status' => Endpoint::STATUS_DISABLED]
        );
        $instance->setStatus('fake');

        self::assertEquals(Endpoint::STATUS_DISABLED, $instance->getStatus());
    }

    /**
     * test status can't be set to something weird.
     * 
     * @throws \InvalidArgumentException
     */
    public function testCreateFromString()
    {
        $endpoint = Endpoint::createFromString('http://www.blabla.com');
        self::assertInstanceOf('\WebservicesNl\Common\Endpoint\Endpoint', $endpoint);
    }
}
