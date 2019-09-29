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

namespace ConstanzeStandard\Dependency\Interfaces;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Add a entry to entry aggregate.
     * 
     * @param EntryInterface $entry
     * 
     * @return EntryInterface
     */
    public function addEntry(EntryInterface $entry);

    /**
     * Add a entry or definition of entry to container.
     * 
     * @param string $id Identifier of the entry.
     * @param mixed $entry A entry or definition of entry.
     * @param bool $isDefinition Entry is definition?
     * 
     * @return EntryInterface
     */
    public function add(string $id, $entry, bool $isDefinition = false);

    /**
     * Build a new entry by definition id and parameters.
     * 
     * @param string $id
     * @param mixed $parameters
     */
    public function make(string $id, ...$parameters);

    /**
     * Add a service provider.
     * 
     * @param ServiceProviderInterface $serviceProvider
     * 
     * @return self
     */
    public function addServiceProvider(ServiceProviderInterface $serviceProvider): self;
}
