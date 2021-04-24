<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\EntityCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderInterface;

require_once __DIR__ . '/AbstractTest.php';

class ContainerTest extends AbstractTest
{
    public function testAddEntry()
    {
        /**
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        // $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

        $entry->expects($this->once())->method('addArguments')->willReturn($entry);
        $entryCollection->expects($this->once())->method('add')->with($entry)->willReturn($entry);

        $container = new Container($entryCollection);
        $result = $container->addEntity($entry);
        $this->assertEquals($result, $entry);
    }

    public function testAdd()
    {
        $container = new Container();
        $result = $container->add('id', 'entry');
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertEquals($result->getId(), 'id');
        $this->assertEquals($result->resolve(), 'entry');
    }

    public function testOffsetSet()
    {
        /**
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);

        $entryCollection->expects($this->once())->method('add')->willReturn($entry);

        $container = new Container($entryCollection);
        $container['id'] = 'entry';
    }

    public function testWithEntryArguments()
    {
        /**
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        // $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

        $entry->expects($this->once())->method('addArguments')->with(1, 2)->willReturn($entry);
        $entryCollection->expects($this->once())->method('add')->with($entry)->willReturn($entry);

        $container = new Container($entryCollection);
        $result = $container->withEntityArguments(1, 2);
        $this->assertEquals($result, $container);
        $result = $container->addEntity($entry);
        $this->assertEquals($result, $entry);
    }

    public function testMake()
    {
        /**
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->expects($this->once())->method('resolve')->with('id', [1,2], true)->willReturn(1);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->make('id', 1, 2);
        $this->assertEquals($result, 1);
    }

    public function testMakeWithProvider()
    {
        /**
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

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
         * @var EntityInterface $entry
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entry = $this->createMock(EntityInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $container->make('id', 1, 2);
    }

    public function testAddEntryProvider()
    {
        /**
         * @var EntityProviderInterface $entryProvider
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryProvider = $this->createMock(EntityProviderInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);

        $entryProviderCollection->expects($this->once())->method('add')->with($entryProvider);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->addEntityProvider($entryProvider);
        $this->assertEquals($result, $container);
    }

    public function testRemove()
    {
        /**
         * @var EntityProviderInterface $entryProvider
         * @var EntityCollectionInterface $entryCollection
         */
        $entryProvider = $this->createMock(EntityProviderInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryCollection->expects($this->once())->method('remove')->with('id');

        $container = new Container($entryCollection);
        $result = $container->remove('id');
    }

    public function testOffsetUnset()
    {
        /**
         * @var EntityProviderInterface $entryProvider
         * @var EntityCollectionInterface $entryCollection
         */
        $entryProvider = $this->createMock(EntityProviderInterface::class);
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertTrue($result);
    }

    public function testOffsetExistsInEntryCollection()
    {
        /**
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = isset($container['id']);;
        $this->assertTrue($result);
    }

    public function testHasInEntryProviderCollection()
    {
        /**
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertTrue($result);
    }

    public function testOffsetExistsInEntryProviderCollection()
    {
        /**
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryCollection->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = isset($container['id']);;
        $this->assertTrue($result);
    }

    public function testHasNotFound()
    {
        /**
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
        $entryProviderCollection->expects($this->once())->method('has')->with('id')->willReturn(false);
        $entryCollection->expects($this->once())->method('has')->with('id')->willReturn(false);

        $container = new Container($entryCollection, $entryProviderCollection);
        $result = $container->has('id');
        $this->assertFalse($result);
    }

    public function testOffsetExistsNotFound()
    {
        /**
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
         * @var EntityCollectionInterface $entryCollection
         * @var EntityProviderCollectionInterface $entryProviderCollection
         */
        $entryCollection = $this->createMock(EntityCollectionInterface::class);
        $entryProviderCollection = $this->createMock(EntityProviderCollectionInterface::class);
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
