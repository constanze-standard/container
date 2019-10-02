<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\EntryInterface;

require_once __DIR__ . '/AbstractTest.php';

class EntryCollectionTest extends AbstractTest
{
    public function testGetWithId()
    {
        /** @var EntryInterface $entry */
        $entry = $this->createMock(EntryInterface::class);
        $entry->expects($this->once())->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getIdentifier')->willReturn('id');
        $entryCollection = new EntryCollection();
        $entryCollection->add($entry);
        $result = $entryCollection->get('id');
        $this->assertEquals($result, 10);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetWithoutId()
    {
        /** @var EntryInterface $entry */
        $entry = $this->createMock(EntryInterface::class);
        $entry->expects($this->exactly(0))->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getIdentifier')->willReturn('id');
        $entryCollection = new EntryCollection();
        $entryCollection->add($entry);
        $entryCollection->get('nothing');
    }

    public function testGetWithIdWithAlias()
    {
        /** @var EntryInterface $entry */
        $entry = $this->createMock(EntryInterface::class);
        $entry->expects($this->once())->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getIdentifier')->willReturn('id');
        $entryCollection = new EntryCollection();
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
        /** @var EntryInterface $entry */
        $entry = $this->createMock(EntryInterface::class);
        $entry->expects($this->exactly(0))->method('resolve')->willReturn(10);
        $entry->expects($this->once())->method('getIdentifier')->willReturn('id');
        $entryCollection = new EntryCollection();
        $entryCollection->add($entry);
        $entryCollection->remove('id');
        $entryCollection->get('id');
    }

    public function testResolveWithConstructValue()
    {
        /** @var EntryInterface $entry */
        $entry = $this->createMock(EntryInterface::class);
        $entry->expects($this->once())->method('resolve')->with([1, 2], true)->willReturn(10);
        $entry->expects($this->once())->method('getIdentifier')->willReturn('id');
        $entryCollection = new EntryCollection([$entry]);
        $result = $entryCollection->resolve('id', [1, 2], true);
        $this->assertEquals($result, 10);
    }
}
