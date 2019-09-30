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

use ConstanzeStandard\Container\Interfaces\BootableEntryProviderInterface;
use ConstanzeStandard\Container\Interfaces\ContainerInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderInterface;

class EntryProviderCollection implements EntryProviderCollectionInterface
{
    /**
     * entry provides.
     * 
     * @var EntryProviderInterface[]
     */
    private $entryProviders = [];

    /**
     * Registered entry provides names.
     * 
     * @var string[]
     */
    private $registered = [];

    /**
     * The dependency container.
     * 
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container The dependency container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns true if the entry is provided by collection or returns false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool
    {
        foreach ($this->entryProviders as $entryProvider) {
            if ($entryProvider->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a entry provider to collection.
     * 
     * @param EntryProviderInterface $entryProvider
     * 
     * @return self
     */
    public function add(EntryProviderInterface $entryProvider): EntryProviderCollectionInterface
    {
        if (in_array($entryProvider, $this->entryProviders, true)) {
            return $this;
        }

        if ($entryProvider instanceof BootableEntryProviderInterface) {
            $entryProvider->boot($this->container);
        }

        $this->entryProviders[] = $entryProvider;
        return $this;
    }

    /**
     * Register items with the container.
     * 
     * @param ContainerInterface $container
     * 
     * @return void
     */
    public function register($id)
    {
        foreach ($this->entryProviders as $entryProvider) {
            $providerName = get_class($entryProvider);
            if (
                ! in_array($providerName, $this->registered, true) &&
                $entryProvider->has($id)
            ) {
                // don't break
                $entryProvider->register($this->container);
                $this->registered[] = $providerName;
            }
        }
    }
}
