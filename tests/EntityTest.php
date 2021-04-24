<?php

use ConstanzeStandard\Container\Entity;
use ConstanzeStandard\Container\Interfaces\EntityInterface;

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
}
