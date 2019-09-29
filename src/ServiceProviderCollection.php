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

namespace ConstanzeStandard\Dependency;

use ConstanzeStandard\Dependency\Interfaces\BootableServiceProviderInterface;
use ConstanzeStandard\Dependency\Interfaces\ContainerInterface;
use ConstanzeStandard\Dependency\Interfaces\ServiceProviderCollectionInterface;
use ConstanzeStandard\Dependency\Interfaces\ServiceProviderInterface;

class ServiceProviderCollection implements ServiceProviderCollectionInterface
{
    /**
     * service provides.
     * 
     * @var ServiceProviderInterface[]
     */
    private $serviceProviders = [];

    /**
     * Registered service provides names.
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
     * Returns true if the service is provided by collection or returns false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            if ($serviceProvider->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a service provider to collection.
     * 
     * @param ServiceProviderInterface $serviceProvider
     * 
     * @return self
     */
    public function add(ServiceProviderInterface $serviceProvider): ServiceProviderCollectionInterface
    {
        if (in_array($serviceProvider, $this->serviceProviders, true)) {
            return $this;
        }

        if ($serviceProvider instanceof BootableServiceProviderInterface) {
            $serviceProvider->boot($this->container);
        }

        $this->serviceProviders[] = $serviceProvider;
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
        foreach ($this->serviceProviders as $serviceProvider) {
            $providerName = get_class($serviceProvider);
            if (
                ! in_array($providerName, $this->registered, true) &&
                $serviceProvider->has($id)
            ) {
                // don't break
                $serviceProvider->register($this->container);
                $this->registered[] = $providerName;
            }
        }
    }
}
