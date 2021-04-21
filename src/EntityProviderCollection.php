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

use ConstanzeStandard\Container\Interfaces\BootableEntityProviderInterface;
use ConstanzeStandard\Container\Interfaces\ContainerInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderCollectionInterface;
use ConstanzeStandard\Container\Interfaces\EntityProviderInterface;

class EntityProviderCollection implements EntityProviderCollectionInterface
{
    /**
     * entity provides.
     * 
     * @var EntityProviderInterface[]
     */
    private array $entityProviders = [];

    /**
     * Registered entity provides names.
     * 
     * @var string[]
     */
    private array $registered = [];

    /**
     * The dependency container.
     * 
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @param ContainerInterface $container The dependency container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns true if the entity is provided by collection or returns false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool
    {
        foreach ($this->entityProviders as $entityProvider) {
            if ($entityProvider->has($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add a entity provider to collection.
     * 
     * @param EntityProviderInterface $entityProvider
     * 
     * @return EntityProviderCollectionInterface
     */
    public function add(EntityProviderInterface $entityProvider): EntityProviderCollectionInterface
    {
        if (in_array($entityProvider, $this->entityProviders, true)) {
            return $this;
        }

        if ($entityProvider instanceof BootableEntityProviderInterface) {
            $entityProvider->boot($this->container);
        }

        $this->entityProviders[] = $entityProvider;
        return $this;
    }

    /**
     * Register items with the container.
     *
     * @param $id
     * @return void
     */
    public function register($id)
    {
        foreach ($this->entityProviders as $entityProvider) {
            $providerName = get_class($entityProvider);
            if (
                ! in_array($providerName, $this->registered, true) &&
                $entityProvider->has($id)
            ) {
                // don't break
                $entityProvider->register($this->container);
                $this->registered[] = $providerName;
            }
        }
    }
}
