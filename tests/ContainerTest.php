<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\EntryCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntryInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderInterface;

require_once __DIR__ . '/AbstractTest.php';

class ContainerTest extends AbstractTest
{
    public function testAddEntry()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        // $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entry->expects($this->once())->method('addArguments')->willReturn($entry);
        $entryCollection->expects($this->once())->method('add')->with($entry)->willReturn($entry);

        $container = new Container($entryCollection);
        $result = $container->addEntry($entry);
        $this->assertEquals($result, $entry);
    }

    public function testAdd()
    {
        $container = new Container();
        $result = $container->add('id', 'entry');
        $this->assertInstanceOf(EntryInterface::class, $result);
        $this->assertEquals($result->getIdentifier(), 'id');
        $this->assertEquals($result->resolve(), 'entry');
    }

    public function testOffsetSet()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);

        $entryCollection->expects($this->once())->method('add')->willReturn($entry);

        $container = new Container($entryCollection);
        $container['id'] = 'entry';
    }

    public function testWithEntryArguments()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        // $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entry->expects($this->once())->method('addArguments')->with(1, 2)->willReturn($entry);
        $entryCollection->expects($this->once())->method('add')->with($entry)->willReturn($entry);

        $container = new Container($entryCollection);
        $result = $container->withEntryArguments(1, 2);
        $this->assertEquals($result, $container);
        $result = $container->addEntry($entry);
        $this->assertEquals($result, $entry);
    }

    public function testMake()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->expects($this->once())->method('resolve')->with('id', [1,2], true)->willReturn(1);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->make('id', 1, 2);
        $this->assertEquals($result, 1);
    }

    public function testMakeWithProvider()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('resolve')->with('id', [1,2], true)->willReturn(1);

        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->expects($this->once())->method('register')->with('id');

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->make('id', 1, 2);
        $this->assertEquals($result, 1);
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testMakeWithException()
    {
        /**
         * @var EntryInterface $entry
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntryInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $container->make('id', 1, 2);
    }

    public function testAddEntryProvider()
    {
        /**
         * @var EntryProviderInterface $entryProvider
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryProvider = $this->createMock(EntryProviderInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);

        $entryProviderCollection->expects($this->once())->method('add')->with($entryProvider);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->addEntryProvider($entryProvider);
        $this->assertEquals($result, $container);
    }

    public function testRemove()
    {
        /**
         * @var EntryProviderInterface $entryProvider
         * @var EntryCollectionInterface $entryCollection
         */
        $entryProvider = $this->createMock(EntryProviderInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryCollection->expects($this->once())->method('remove')->with('id');

        $container = new Container($entryCollection);
        $result = $container->remove('id');
    }

    public function testOffsetUnset()
    {
        /**
         * @var EntryProviderInterface $entryProvider
         * @var EntryCollectionInterface $entryCollection
         */
        $entryProvider = $this->createMock(EntryProviderInterface::class);
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryCollection->expects($this->once())->method('remove')->with('id');

        $container = new Container($entryCollection);
        unset($container['id']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasWithoutString()
    {
        $container = new Container();
        $container->has(123);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetExistsWithoutString()
    {
        $container = new Container();
        isset($container[123]);
    }

    public function testHasInEntryCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertTrue($result);
    }

    public function testOffsetExistsInEntryCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = isset($container['id']);;
        $this->assertTrue($result);
    }

    public function testHasInEntryProviderCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertTrue($result);
    }

    public function testOffsetExistsInEntryProviderCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = isset($container['id']);;
        $this->assertTrue($result);
    }

    public function testHasNotFound()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertFalse($result);
    }

    public function testOffsetExistsNotFound()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = isset($container['id']);;
        $this->assertFalse($result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetException()
    {
        $container = new Container();
        $container->get(123);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetGetException()
    {
        $container = new Container();
        $container[123];
    }

    public function testGetInEntryCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->expects($this->once())->method('get')->with('id')->willReturn(123);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->get('id');
        $this->assertEquals(123, $result);
    }

    public function testOffsetGetInEntryCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->expects($this->once())->method('get')->with('id')->willReturn(123);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container['id'];
        $this->assertEquals(123, $result);
    }

    public function testGetInEntryProviderCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->expects($this->once())->method('register')->with('id');
        $entryCollection->expects($this->once())->method('get')->with('id')->willReturn(123);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->get('id');
        $this->assertEquals(123, $result);
    }

    public function testOffsetGetInEntryProviderCollection()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->expects($this->once())->method('register')->with('id');
        $entryCollection->expects($this->once())->method('get')->with('id')->willReturn(123);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container['id'];
        $this->assertEquals(123, $result);
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetNotFound()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $container->get('id');
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testOffsetGetNotFound()
    {
        /**
         * @var EntryCollectionInterface $entryCollection
         * @var EntryProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntryCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntryProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $container['id'];
    }

    public function testAlias()
    {
        $container = new Container();
        $container->add('foo', 123);
        $container->alias('bar', 'foo');
        $this->assertEquals(123, $container->get('bar'));
    }
}
