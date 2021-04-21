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

interface EntityInterface
{
    /**
     * Get the entity id.
     * 
     * @return string
     */
    public function getId(): string;

    /**
     * Handle instantiation and return value.
     * 
     * @param array $arguments Parameters of definition.
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(array $arguments = [], bool $new = false): mixed;

    /**
     * Add resolve arguments.
     * 
     * @param mixed ...$arguments
     * 
     * @return self
     */
    public function addArguments(...$arguments): self;
}
