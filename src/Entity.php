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

use ConstanzeStandard\Container\Interfaces\EntityInterface;

class Entity implements EntityInterface
{
    const TYPE_VALUE = 1;

    const TYPE_DEFINITION = 2;

    /**
     * The entity id.
     * 
     * @var string
     */
    private string $id;

    /**
     * The entity.
     * 
     * @var mixed
     */
    private mixed $entity;

    /**
     * @var string Entity type
     */
    private string $type;

    /**
     * Resolved value.
     * 
     * @var mixed
     */
    private mixed $resolved = false;

    /**
     * Value of entity.
     * 
     * @var mixed
     */
    private mixed $value;

    /**
     * Resolve arguments.
     * 
     * @var array
     */
    private array $arguments = [];

    /**
     * @param string $id
     * @param mixed $entity
     * @param bool $isDefinition
     */
    public function __construct(string $id, mixed $entity, int $type = self::TYPE_VALUE)
    {
        $this->id = $id;
        $this->entity = $entity;
        $this->type = $type;
        $this->value = $this->isDefinition ? null : $entity;
    }

    /**
     * Get the entity id.
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Add resolve arguments.
     * 
     * @param mixed ...$arguments
     * 
     * @return self
     */
    public function addArguments(...$arguments): EntityInterface
    {
        if ()
        if ($this->isDefinition) {
            $this->arguments = array_merge(
                $this->arguments,
                array_values($arguments)
            );
        }

        return (clone $this);
    }

    public function isDefinition()
    {
        return $this->type === self::TYPE_DEFINITION;
    }

    /**
     * Handle instantiation and return value.
     * 
     * @param array $arguments Parameters of definition.
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(array $arguments = [], bool $new = false): mixed
    {
        if (! $this->isDefinition || ($this->resolved && $new === false)) {
            return $this->value;
        }

        $this->resolved = true;
        $this->value = call_user_func(
            $this->entity, ...$this->arguments, ...$arguments
        );

        return $this->value;
    }
}
