<?php

/**
 * Copyright 2019 Constanze Standard <omytty.alex@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ConstanzeStandard\Container;

use ArrayAccess;
use ConstanzeStandard\Container\Exception\NotFoundException;
use ConstanzeStandard\Container\Interfaces\ContainerInterface;
use ConstanzeStandard\Container\Interfaces\EntryCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntryInterface;
use ConstanzeStandard\Container\Interfaces\FactoryInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderInterface;
use InvalidArgumentException;

class Container implements ContainerInterface, FactoryInterface, ArrayAccess
{
    /**
     * The entry aggregate.
     * 
     * @var EntryCollectionInterface
     */
    private $entryCollection;

    /**
     * The entry provider collection.
     * 
     * @var EntryProviderCollectionInterface
     */
    private $entryProviderCollection;

    /**
     * Arguments for every entry.
     * 
     * @var array
     */
    private $entryArguments = [];

    /**
     * @param EntryCollectionInterface|null $entryCollection
     * @param EntryProviderCollectionInterface|null $entryProviderCollection
     */
    public function __construct(
        EntryCollectionInterface $entryCollection = null,
        EntryProviderCollectionInterface $entryProviderCollection = null
    )
    {
        $this->entryCollection = $entryCollection ?? new EntryCollection();
        $this->entryProviderCollection = $entryProviderCollection ?? 
            new EntryProviderCollection($this);
    }

    /**
     * Add a entry to entry aggregate.
     * 
     * @param EntryInterface $entry
     * 
     * @return EntryInterface
     */
    public function addEntry(EntryInterface $entry)
    {
        return $this->entryCollection
            ->add($entry)
            ->addArguments(...$this->entryArguments);
    }

    /**
     * Add a entry or definition of entry to container.
     * 
     * @param string $id Identifier of the entry.
     * @param mixed $entry A entry or definition of entry.
     * @param bool $isDefinition Entry is definition?
     * 
     * @return EntryInterface
     */
    public function add(string $id, $entry, bool $isDefinition = false)
    {
        return $this->addEntry(
            new Entry($id, $entry, $isDefinition)
        );
    }

    /**
     * Add arguments for every entry.
     * 
     * @param mixed ...$arguments
     */
    public function withEntryArguments(...$arguments): self
    {
        $this->entryArguments = array_merge(
            $this->entryArguments,
            $arguments
        );

        return $this;
    }

    /**
     * Build a new entry by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     * 
     * @return mixed
     */
    public function make(string $id, ...$parameters)
    {
        if ($this->entryCollection->has($id)) {
            return $this->entryCollection->resolve($id, $parameters, true);
        }

        if ($this->entryProviderCollection->has($id)) {
            $this->entryProviderCollection->register($id);
            return $this->entryCollection->resolve($id, $parameters, true);
        }

        throw new NotFoundException("No entry found for '$id'");
    }

    /**
     * Add a entry provider.
     * 
     * @param EntryProviderInterface $entryProvider
     * 
     * @return self
     */
    public function addEntryProvider(EntryProviderInterface $entryProvider): ContainerInterface
    {
        $this->entryProviderCollection->add($entryProvider);

        return $this;
    }

    /**
     * Remove an entry from container.
     * 
     * @param string $id
     */
    public function remove(string $id)
    {
        $this->entryCollection->remove($id);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (! is_string($id)) {
            throw new InvalidArgumentException('The first parameter of `'.static::class . '::get` must be string.');
        }

        if ($this->entryCollection->has($id)) {
            return $this->entryCollection->get($id);
        }

        if ($this->entryProviderCollection->has($id)) {
            $this->entryProviderCollection->register($id);
            return $this->entryCollection->get($id);
        }

        throw new NotFoundException("No entry found for '$id'");
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        if (! is_string($id)) {
            throw new InvalidArgumentException('The first parameter of `'.static::class . '::has` must be string.');
        }

        if (
            $this->entryCollection->has($id) ||
            $this->entryProviderCollection->has($id)
        ) {
            return true;
        }

        return false;
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
