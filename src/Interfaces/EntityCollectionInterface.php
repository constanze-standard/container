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

namespace ConstanzeStandard\Container\Interfaces;

interface EntityCollectionInterface
{
    /**
     * Returns true if the entry exist or return false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Get an entry value from aggregate.
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function get(string $id): mixed;

    /**
     * Add a entry to aggregate.
     * 
     * @param EntityInterface $entity
     * 
     * @return EntityInterface
     */
    public function add(EntityInterface $entity): EntityInterface;

    /**
     * Remove a entry from collection.
     * 
     * @param string $id
     */
    public function remove(string $id);

    /**
     * Handle instantiation and return value.
     * 
     * @param string $id
     * @param array $parameters
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(string $id, array $parameters = [], bool $new = false): mixed;

    /**
     * Binding an alias to an entry.
     * 
     * @param string $alias
     * @param string $id
     * 
     * @return EntityInterface
     */
    public function alias(string $alias, string $id): EntityInterface;
}
