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

namespace ConstanzeStandard\Dependency;

use ConstanzeStandard\Dependency\Exception\NotFoundException;
use ConstanzeStandard\Dependency\Interfaces\ContainerInterface;
use ConstanzeStandard\Dependency\Interfaces\EntryCollectionInterface;
use ConstanzeStandard\Dependency\Interfaces\EntryInterface;
use ConstanzeStandard\Dependency\Interfaces\FactoryInterface;
use ConstanzeStandard\Dependency\Interfaces\ServiceProviderCollectionInterface;
use ConstanzeStandard\Dependency\Interfaces\ServiceProviderInterface;
use InvalidArgumentException;

class Container implements ContainerInterface, FactoryInterface
{
    /**
     * The entry aggregate.
     * 
     * @var EntryCollectionInterface
     */
    private $entryAggregate;

    /**
     * The service provider collection.
     * 
     * @var ServiceProviderCollectionInterface
     */
    private $serviceProviderCollection;

    /**
     * @param EntryCollectionInterface|null $entryAggregate
     * @param ServiceProviderCollectionInterface|null $serviceProviderCollection
     */
    public function __construct(
        EntryCollectionInterface $entryAggregate = null,
        ServiceProviderCollectionInterface $serviceProviderCollection = null
    )
    {
        $this->entryAggregate = $entryAggregate ?? new EntryCollection();
        $this->serviceProviderCollection = $serviceProviderCollection ?? 
            new ServiceProviderCollection($this);
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
        return $this->entryAggregate->add($entry);
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
        $entry = new Entry($id, $entry, $isDefinition);
        return $this->addEntry($entry);
    }

    /**
     * Build a new entry by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     */
    public function make(string $id, ...$parameters)
    {
        if ($this->entryAggregate->has($id)) {
            return $this->entryAggregate->resolve($id, $parameters, true);
        }

        if ($this->serviceProviderCollection->has($id)) {
            $this->serviceProviderCollection->register($id);
            return $this->entryAggregate->resolve($id, $parameters, true);
        }

        throw new NotFoundException("No entry found for '$id'");
    }

    /**
     * Add a service provider.
     * 
     * @param ServiceProviderInterface $serviceProvider
     * 
     * @return self
     */
    public function addServiceProvider(ServiceProviderInterface $serviceProvider): ContainerInterface
    {
        $this->serviceProviderCollection->add($serviceProvider);

        return $this;
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

        if ($this->entryAggregate->has($id)) {
            return $this->entryAggregate->get($id);
        }

        if ($this->serviceProviderCollection->has($id)) {
            $this->serviceProviderCollection->register($id);
            return $this->entryAggregate->get($id);
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
            $this->entryAggregate->has($id) ||
            $this->serviceProviderCollection->has($id)
        ) {
            return true;
        }

        return false;
    }
}
