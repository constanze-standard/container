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

use ConstanzeStandard\Dependency\Interfaces\EntryCollectionInterface;
use ConstanzeStandard\Dependency\Interfaces\EntryInterface;
use RuntimeException;

class EntryCollection implements EntryCollectionInterface
{
    /**
     * Entries.
     * 
     * @var EntryInterface[]
     */
    private $entries = [];

    /**
     * @param array $entries
     */
    public function __construct(array $entries = [])
    {
        foreach ($entries as $entry) {
            $this->add($entry);
        }
    }

    /**
     * Add a entry to aggregate.
     * 
     * @param EntryInterface $entry
     * 
     * @return EntryInterface
     */
    public function add(EntryInterface $entry): EntryInterface
    {
        $this->entries[$entry->getIdentifier()] = $entry;
        return $entry;
    }

    /**
     * Get an entry value from aggregate.
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->getEntry($id)->resolve();
    }

    /**
     * Returns true if the entry exist or return false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * Handle instantiation and return value.
     * 
     * @param string $id
     * @param array $parameters
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(string $id, array $parameters = [], bool $new = false)
    {
        return $this->getEntry($id)->resolve($parameters, $new);
    }

    /**
     * Get an entry from aggregate.
     * 
     * @param string $id
     * 
     * @return EntryInterface
     */
    private function getEntry(string $id): EntryInterface
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            return $entry;
        }

        throw new RuntimeException("No entry found for '$id'");
    }
}
