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

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Add a entity to entry aggregate.
     * 
     * @param EntityInterface $entry
     * 
     * @return EntityInterface
     */
    public function addEntity(EntityInterface $entry): EntityInterface;

    /**
     * Add a entry or definition of entry to container.
     * 
     * @param string $id Identifier of the entry.
     * @param mixed $entity A entry or definition of entry.
     * @param bool $isDefinition Entity is definition?
     * 
     * @return EntityInterface
     */
    public function add(string $id, mixed $entity, bool $isDefinition = false): EntityInterface;

    /**
     * Build a new entry by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     */
    public function make(string $id, ...$parameters);

    /**
     * Add a entry provider.
     * 
     * @param EntityProviderInterface $entryProvider
     * 
     * @return self
     */
    public function addEntryProvider(EntityProviderInterface $entryProvider): self;

    /**
     * Remove an entry from container.
     * 
     * @param string $id
     */
    public function remove(string $id);

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
