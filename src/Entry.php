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

use ConstanzeStandard\Container\Interfaces\EntryInterface;

class Entry implements EntryInterface
{
    /**
     * The entry id.
     * 
     * @var string
     */
    private $id;

    /**
     * The entry.
     * 
     * @var mixed
     */
    private $entry;

    /**
     * The entry is a definition.
     * 
     * @var bool
     */
    private $isDefinition;

    /**
     * Resolved value.
     * 
     * @var mixed
     */
    private $resolved = false;

    /**
     * Value of entry.
     * 
     * @var mixed
     */
    private $value;

    /**
     * Resolve arguments.
     * 
     * @var array
     */
    private $arguments = [];

    /**
     * @param string $id
     * @param mixed $entry
     * @param bool $isDefinition
     */
    public function __construct(string $id, $entry, bool $isDefinition = false)
    {
        $this->id = $id;
        $this->entry = $entry;
        $this->isDefinition = $isDefinition ? is_callable($entry) : false;
        $this->value = $this->isDefinition ? null : $entry;
    }

    /**
     * Get the entry id.
     * 
     * @return string
     */
    public function getIdentifier(): string
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
    public function addArguments(...$arguments): EntryInterface
    {
        if ($this->isDefinition) {
            $this->arguments = array_merge(
                $this->arguments,
                array_values($arguments)
            );
        }

        return $this;
    }

    /**
     * Handle instantiation and return value.
     * 
     * @param array $arguments Parameters of definition.
     * @param bool $new
     * 
     * @return mixed
     */
    public function resolve(array $arguments = [], bool $new = false)
    {
        if (! $this->isDefinition || ($this->resolved && $new === false)) {
            return $this->value;
        }

        $this->resolved = true;
        $this->value = call_user_func(
            $this->entry, ...$this->arguments, ...$arguments
        );

        return $this->value;
    }
}
