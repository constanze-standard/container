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
use ConstanzeStandard\Container\Interfaces\EntityProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface, ArrayAccess
{
    /**
     * The entity aggregate.
     * 
     * @var EntityCollectionInterface
     */
    private EntityCollectionInterface $entityCollection;

    /**
     * The entity provider collection.
     * 
     * @var EntityProviderCollectionInterface
     */
    private EntityProviderCollectionInterface $entityProviderCollection;

    /**
     * Arguments for every entity.
     * 
     * @var array
     */
    private array $entityArguments = [];

    /**
     * @param EntityCollectionInterface|null $entityCollection
     * @param EntityProviderCollectionInterface|null $entityProviderCollection
     */
    public function __construct(
        EntityCollectionInterface $entityCollection = null,
        EntityProviderCollectionInterface $entityProviderCollection = null
    )
    {
        $this->entityCollection = $entityCollection ?? new EntityCollection();
        $this->entityProviderCollection = $entityProviderCollection ??
            new EntityProviderCollection($this);
    }

    /**
     * Add a entity to entity aggregate.
     * 
     * @param EntityInterface $entity
     * 
     * @return EntityInterface
     */
    public function addEntity(EntityInterface $entity): EntityInterface
    {
        return $this->entityCollection
            ->add($entity)
            ->addArguments(...$this->entityArguments);
    }

    /**
     * Add a entity or definition of entity to container.
     *
     * @param string $id Identifier of the entity.
     * @param mixed $entity A entity or definition of entity.
     * @param int $type
     *
     * @return EntityInterface
     */
    public function add(string $id, mixed $entity, int $type = EntityInterface::TYPE_VALUE): EntityInterface
    {
        return $this->addEntity(
            new Entity($id, $entity, $type)
        );
    }

    /**
     * Add arguments for every entity.
     * 
     * @param mixed ...$arguments
     */
    public function withEntityArguments(...$arguments): self
    {
        $this->entityArguments = array_merge(
            $this->entityArguments,
            $arguments
        );
        return $this;
    }

    /**
     * Build a new entity by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     * 
     * @return mixed
     */
    public function make(string $id, ...$parameters): mixed
    {
        if ($this->entityCollection->has($id)) {
            return $this->entityCollection->resolve($id, $parameters, true);
        }

        if ($this->entityProviderCollection->has($id)) {
            $this->entityProviderCollection->register($id);
            return $this->entityCollection->resolve($id, $parameters, true);
        }

        throw new NotFoundException("Entity not found with key '$id'");
    }

    /**
     * Add a entity provider.
     * 
     * @param EntityProviderInterface $entityProvider
     * 
     * @return self
     */
    public function addEntityProvider(EntityProviderInterface $entityProvider): self
    {
        $this->entityProviderCollection->add($entityProvider);

        return $this;
    }

    /**
     * Remove an entity from container.
     * 
     * @param string $id
     */
    public function remove(string $id)
    {
        $this->entityCollection->remove($id);
    }

    /**
     * Binding an alias to an entity.
     * 
     * @param string $alias
     * @param string $id
     */
    public function alias(string $alias, string $id)
    {
        $this->entityCollection->alias($alias, $id);
    }

    /**
     * Finds an entity of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entity to look for.
     *
     * @return mixed Entity.
     *@throws ContainerExceptionInterface Error while retrieving the entity.
     *
     * @throws NotFoundExceptionInterface  No entity was found for **this** identifier.
     */
    public function get(string $id): mixed
    {
        if (! is_string($id)) {
            throw new InvalidArgumentException('The first parameter of `'.static::class . '::get` must be string.');
        }

        if ($this->entityCollection->has($id)) {
            return $this->entityCollection->get($id);
        }

        if ($this->entityProviderCollection->has($id)) {
            $this->entityProviderCollection->register($id);
            return $this->entityCollection->get($id);
        }

        throw new NotFoundException("Entity not found with key '$id'");
    }

    /**
     * Returns true if the container can return an entity for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entity to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        if (! is_string($id)) {
            throw new InvalidArgumentException('The first parameter of `'.static::class . '::has` must be string.');
        }

        if (
            $this->entityCollection->has($id) ||
            $this->entityProviderCollection->has($id)
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
