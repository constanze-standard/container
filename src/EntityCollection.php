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

use ConstanzeStandard\Container\Interfaces\EntityCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityInterface;
use RuntimeException;

class EntityCollection implements EntityCollectionInterface
{
    /**
     * Entities.
     * 
     * @var EntityInterface[]
     */
    private array $entities = [];

    /**
     * @param array $entities
     */
    public function __construct(array $entities = [])
    {
        if ($entities) {
            foreach ($entities as $entity) {
                $this->add($entity);
            }
        }
    }

    /**
     * Add a entity to aggregate.
     * 
     * @param EntityInterface $entity
     * 
     * @return EntityInterface
     */
    public function add(EntityInterface $entity): EntityInterface
    {
        $this->entities[$entity->getId()] = $entity;
        return $entity;
    }

    /**
     * Binding an alias to an entity.
     * 
     * @param string $alias
     * @param string $id
     * 
     * @return EntityInterface
     */
    public function alias(string $alias, string $id): EntityInterface
    {
        $entity = $this->getEntity($id);
        $this->entities[$alias] = &$entity;
        return $entity;
    }

    /**
     * Get an entity value from aggregate.
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->getEntity($id)->resolve();
    }

    /**
     * Returns true if the entity exist or return false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entities);
    }

    /**
     * Remove a entity from collection.
     * 
     * @param string $id
     */
    public function remove(string $id)
    {
        unset($this->entities[$id]);
    }

    /**
     * Handle instantiation and return value.
     * 
     * @param string $id
     * @param array $parameters
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(string $id, array $parameters = [], bool $new = false): mixed
    {
        return $this->getEntity($id)->resolve($parameters, $new);
    }

    /**
     * Get an entity from aggregate.
     * 
     * @param string $id
     * 
     * @return EntityInterface
     */
    private function getEntity(string $id): EntityInterface
    {
        if ($this->has($id)) {
            return $this->entities[$id];
        }

        throw new RuntimeException("No entity found for '$id'");
    }
}
