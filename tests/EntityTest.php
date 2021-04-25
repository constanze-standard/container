<?php

namespace test;

use ConstanzeStandard\Container\Entity;
use ConstanzeStandard\Container\Interfaces\EntityInterface;
use ReflectionException;

/**
 * Class EntityTest
 * @package test
 * @coversDefaultClass Entity
 */
class EntityTest extends AbstractTest
{
    /**
     * @throws ReflectionException
     */
    public function test__construct_with_type_value()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_VALUE);
        $this->assertSame('id1', $this->getProperty($entry, 'id'));
        $this->assertSame('entity1', $this->getProperty($entry, 'entity'));
        $this->assertSame(EntityInterface::TYPE_VALUE, $this->getProperty($entry, 'type'));
        $this->assertSame('entity1', $this->getProperty($entry, 'value'));
    }

    /**
     * @throws ReflectionException
     */
    public function test__construct_with_type_definition()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_DEFINITION);
        $this->assertSame(EntityInterface::TYPE_DEFINITION, $this->getProperty($entry, 'type'));
        $this->assertNull($this->getProperty($entry, 'value'));
    }

    public function test_get_id()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_DEFINITION);
        $this->assertSame('id1', $entry->getId());
    }

    public function test_is_definition_with_true()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_DEFINITION);
        $this->assertTrue($entry->isDefinition());
    }

    public function test_is_definition_with_false()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_VALUE);
        $this->assertFalse($entry->isDefinition());
    }

    /**
     * @throws ReflectionException
     */
    public function test_add_arguments_with_definition()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_DEFINITION);
        $result = $entry->addArguments('arg1', 'arg2');
        $result = $result->addArguments('arg3');
        $arguments = $this->getProperty($result, 'arguments');
        $this->assertIsArray($arguments);
        $this->assertCount(3, $arguments);
        $this->assertTrue(in_array('arg1', $arguments));
        $this->assertTrue(in_array('arg2', $arguments));
        $this->assertTrue(in_array('arg3', $arguments));
    }

    /**
     * @throws ReflectionException
     */
    public function test_add_arguments_with_value()
    {
        $entry = new Entity('id1', 'entity1', EntityInterface::TYPE_VALUE);
        $result = $entry->addArguments('arg1', 'arg2');
        $arguments = $this->getProperty($result, 'arguments');
        $this->assertSame([], $arguments);
    }

    public function test_resolve_with_definition_without_new_value()
    {
        $counter = 1;
        $entry = new Entity('id1', function ($value) use (&$counter) {
            return $value . $counter++;
        }, EntityInterface::TYPE_DEFINITION);
        $result1 = $entry->resolve(['value']);
        $result2 = $entry->resolve(['value']);
        $this->assertSame('value1', $result1);
        $this->assertSame('value1', $result2);
    }

    public function test_resolve_with_definition_without_new_value_with_arguments()
    {
        $counter = 1;
        $entry = new Entity('id1', function ($v1, $v2) use (&$counter) {
            return $v1 . $v2 . $counter++;
        }, EntityInterface::TYPE_DEFINITION);
        $entry->addArguments('value1');
        $result1 = $entry->resolve(['value2']);
        $result2 = $entry->resolve(['value3']);
        $this->assertSame('value1value21', $result1);
        $this->assertSame('value1value21', $result2);
    }

    public function test_resolve_with_value_without_new_value()
    {
        $entry = new Entity('id1', 'value', EntityInterface::TYPE_VALUE);
        $result = $entry->resolve(['value1']);
        $this->assertSame('value', $result);
    }

    public function test_resolve_with_value_without_new_value_with_arguments()
    {
        $entry = new Entity('id1', 'value', EntityInterface::TYPE_VALUE);
        $entry->addArguments('value1');
        $result = $entry->resolve(['value2']);
        $this->assertSame('value', $result);
    }

    public function test_resolve_with_definition_with_new_value()
    {
        $counter = 1;
        $entry = new Entity('id1', function () use (&$counter) {
            return $counter++;
        }, EntityInterface::TYPE_DEFINITION);
        $result1 = $entry->resolve([]);
        $result2 = $entry->resolve([], true);
        $this->assertSame(1, $result1);
        $this->assertSame(2, $result2);
    }
}
