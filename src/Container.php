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
use ConstanzeStandard\Container\Interfaces\EntityCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityInterface;
use ConstanzeStandard\Container\Interfaces\FactoryInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface, FactoryInterface, ArrayAccess
{
    /**
     * The entity aggregate.
     * 
     * @var EntityCollectionInterface
     */
    private EntityCollectionInterface $entryCollection;

    /**
     * The entity provider collection.
     * 
     * @var EntityProviderCollectionInterface
     */
    private EntityProviderCollectionInterface $entryProviderCollection;

    /**
     * Arguments for every entity.
     * 
     * @var array
     */
    private array $entityArguments = [];

    /**
     * @param EntityCollectionInterface|null $entryCollection
     * @param EntityProviderCollectionInterface|null $entryProviderCollection
     */
    public function __construct(
        EntityCollectionInterface $entryCollection = null,
        EntityProviderCollectionInterface $entryProviderCollection = null
    )
    {
        $this->entryCollection = $entryCollection ?? new EntityCollection();
        $this->entryProviderCollection = $entryProviderCollection ?? 
            new EntityProviderCollection($this);
    }

    /**
     * Add a entry to entry aggregate.
     * 
     * @param EntityInterface $entity
     * 
     * @return EntityInterface
     */
    public function addEntity(EntityInterface $entity): EntityInterface
    {
        return $this->entryCollection
            ->add($entity)
            ->addArguments(...$this->entityArguments);
    }

    /**
     * Add a entry or definition of entry to container.
     * 
     * @param string $id Identifier of the entry.
     * @param mixed $entity A entry or definition of entry.
     * @param bool $isDefinition Entity is definition?
     * 
     * @return EntityInterface
     */
    public function add(string $id, mixed $entity, bool $isDefinition = false): EntityInterface
    {
        return $this->addEntity(
            new Entity($id, $entity, $isDefinition)
        );
    }

    /**
     * Add arguments for every entry.
     * 
     * @param mixed ...$arguments
     */
    public function withEntryArguments(...$arguments): self
    {
        $this->entityArguments = array_merge(
            $this->entityArguments,
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
    public function make(string $id, ...$parameters): mixed
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
     * @param EntityProviderInterface $entryProvider
     * 
     * @return self
     */
    public function addEntryProvider(EntityProviderInterface $entryProvider): self
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
     * Binding an alias to an entry.
     * 
     * @param string $alias
     * @param string $id
     * 
     * @return EntityInterface
     */
    public function alias(string $alias, string $id): EntityInterface
    {
        return $this->entryCollection->alias($alias, $id);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entity.
     *@throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get(string $id): mixed
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
    public function has(string $id): bool
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
