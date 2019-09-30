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

interface EntryCollectionInterface
{
    /**
     * Returns true if the entry exist or return false.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Get an entry value from aggregate.
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function get(string $id);

    /**
     * Add a entry to aggregate.
     * 
     * @param EntryInterface $entry
     * 
     * @return EntryInterface
     */
    public function add(EntryInterface $entry): EntryInterface;
}
