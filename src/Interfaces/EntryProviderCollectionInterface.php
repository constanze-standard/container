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

interface EntryProviderCollectionInterface
{
    /**
     * Returns true if the entry is provided by collection or returns false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Add a entry provider to collection.
     * 
     * @param EntryProviderInterface $entryProvider
     * 
     * @return self
     */
    public function add(EntryProviderInterface $entryProvider): self;

    /**
     * Register items with the container.
     * 
     * @param ContainerInterface $container
     * 
     * @return void
     */
    public function register($id);
}
