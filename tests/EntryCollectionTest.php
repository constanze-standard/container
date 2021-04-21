<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\EntityInterface;

require_once __DIR__ . '/AbstractTest.php';

class EntryCollectionTest extends AbstractTest
{
    public function testGetWithId()
    {
        /** @var EntityInterface $entry */
        $entry = $this->createMock(EntityInterface::class);
        $entry->expects($this->once())->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getId')->willReturn('id');
        $entryCollection = new EntityCollection();
        $entryCollection->add($entry);
        $result = $entryCollection->get('id');
        $this->assertEquals($result, 10);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetWithoutId()
    {
        /** @var EntityInterface $entry */
        $entry = $this->createMock(EntityInterface::class);
        $entry->expects($this->exactly(0))->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getId')->willReturn('id');
        $entryCollection = new EntityCollection();
        $entryCollection->add($entry);
        $entryCollection->get('nothing');
    }

    public function testGetWithIdWithAlias()
    {
        /** @var EntityInterface $entry */
        $entry = $this->createMock(EntityInterface::class);
        $entry->expects($this->once())->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getId')->willReturn('id');
        $entryCollection = new EntityCollection();
        $entryCollection->add($entry);
        $entryCollection->alias('newId', 'id');
        $result = $entryCollection->get('newId');
        $this->assertEquals($result, 10);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemoveWithId()
    {
        /** @var EntityInterface $entry */
        $entry = $this->createMock(EntityInterface::class);
        $entry->expects($this->exactly(0))->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getId')->willReturn('id');
        $entryCollection = new EntityCollection();
        $entryCollection->add($entry);
        $entryCollection->remove('id');
        $entryCollection->get('id');
    }

    public function testResolveWithConstructValue()
    {
        /** @var EntityInterface $entry */
        $entry = $this->createMock(EntityInterface::class);
        $entry->expects($this->once())->method('resolve')->with([1, 2], true)->willReturn(10);
        $entry->expects($this->once())->method('getId')->willReturn('id');
        $entryCollection = new EntityCollection([$entry]);
        $result = $entryCollection->resolve('id', [1, 2], true);
        $this->assertEquals($result, 10);
    }
}
