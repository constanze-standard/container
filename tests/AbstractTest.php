<?php

namespace test;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

/**
 * Class AbstractTest
 * {@link https://phpunit.readthedocs.io/en/9.5/writing-tests-for-phpunit.html}
 */
abstract class AbstractTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected function setProperty($obj, $property, $value)
    {
        $reflectionProperty = new ReflectionProperty($obj, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($obj, $value);
        $reflectionProperty->setAccessible(false);
    }

    /**
     * @throws ReflectionException
     */
    protected function getProperty($obj, $property)
    {
        $reflectionProperty = new ReflectionProperty($obj, $property);
        $reflectionProperty->setAccessible(true);
        $value = $reflectionProperty->getValue($obj);
        $reflectionProperty->setAccessible(false);
        return $value;
    }

    /**
     * @throws ReflectionException
     */
    protected function callMethod($instance, $methodName, $args = [])
    {
        $method = new ReflectionMethod($instance, $methodName);
        $method->setAccessible(true);
        $result = $method->invokeArgs($instance, $args);
        $method->setAccessible(false);
        return $result;
    }
}