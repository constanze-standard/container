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
     * Add a entity to entity aggregate.
     * 
     * @param EntityInterface $entity
     * 
     * @return EntityInterface
     */
    public function addEntity(EntityInterface $entity): EntityInterface;

    /**
     * Add a entity or definition of entity to container.
     *
     * @param string $id Identifier of the entity.
     * @param mixed $entity A entity or definition of entity.
     * @param int $type
     *
     * @return EntityInterface
     */
    public function add(string $id, mixed $entity, int $type = EntityInterface::TYPE_VALUE): EntityInterface;

    /**
     * Build a new entity by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     *
     * @return mixed
     */
    public function make(string $id, ...$parameters): mixed;

    /**
     * Add a entity provider.
     * 
     * @param EntityProviderInterface $entityProvider
     * 
     * @return self
     */
    public function addEntityProvider(EntityProviderInterface $entityProvider): self;

    /**
     * Remove an entity from container.
     * 
     * @param string $id
     */
    public function remove(string $id);

    /**
     * Binding an alias to an entity.
     * 
     * @param string $alias
     * @param string $id
     */
    public function alias(string $alias, string $id);
}
