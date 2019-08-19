<?php

namespace snuze\Persistence\Interfaces;

use snuze\Persistence\Interfaces\StorageProviderInterface;

/**
 * The MapperInterface is part of the storage provider design.
 *
 * It defines methods that must be implemented by any Mapper object when
 * developing a storage provider for Snuze.
 *
 * *****************************************************************************
 * This file is part of Snuze, a PHP client for the Reddit API.
 * Copyright 2019 Shaun Cummiskey <shaun@shaunc.com> <https://shaunc.com/>
 * Repository: <https://github.com/snuze/snuze/>
 * Documentation: <https://snuze.shaunc.com/>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
interface MapperInterface
{

    /**
     * A Mapper's constructor must accept a storage provider (for example, a
     * MySQL or SQLite storage provider) implementing StorageProviderInterface.
     *
     * @param \snuze\Persistence\Provider\StorageProviderInterface $storage
     */
    public function __construct(StorageProviderInterface $storage);

    /**
     * Persist the object to storage. This must support both the insertion of
     * new objects and the updating of existing objects; implementers should
     * use the equivalent of an "upsert" paradigm.
     *
     * @param object $object The object to persist
     * @return bool True on success; an exception should be thrown on error
     */
    public function persist($object): bool;

    /**
     * Retrieve an object from storage, given a unique identifier for it. The
     * identifier used is left up to the implementer. A SubredditMapper may use
     * the subreddit's name as the default retrieval key, whereas a LinkMapper
     * would use the link's internal Reddit ID, etc. You can always extend the
     * built-in mappers to add your own methods with different criteria.
     *
     * @param mixed $key A unique identifier for the object to be retrieved.
     */
    public function retrieve($key);

    /**
     * Delete an object from storage.
     *
     * @param object $object The object to delete
     * @return bool True on success; an exception should be thrown on error
     */
    public function delete($object): bool;
}
