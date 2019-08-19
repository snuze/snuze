<?php

namespace snuze\Persistence\Interfaces;

/**
 * The StorageProviderInterface is part of the storage provider design.
 *
 * It defines methods that must be implemented by any StorageProvider object
 * when developing a storage provider for Snuze.
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
interface StorageProviderInterface
{

    /**
     * The constructor must accept an array of all parameters necessary to
     * initialize the storage facility. For example, a MySQL provider class
     * might accept an array containing the server name, database name,
     * username, and password.
     *
     * If your provider requires no configuration parameters, it must still
     * accept the array, but can simply disregard it.
     *
     * @param string[] $configurationParameters An array of setup parameters.
     */
    public function __construct(array $configurationParameters = []);

    /**
     * Return an array containing the names of any PHP extensions this provider
     * needs to operate. For example, a SQLite provider class would return an
     * array containing the string 'pdo_sqlite'. This array will be queried
     * by the SnuzeFactory, which will check that the extensions are present.
     *
     * If your provider requires no PHP extensions, return an empty array.
     *
     * @return array
     */
    public function getRequiredExtensionNames(): array;

    /**
     * Perform any actions necessary to initialize the storage facility. For
     * example, a MySQL provider class might obtain a PDO handle to the target
     * database.
     *
     * If your provider requires no initialization, implement an empty method.
     *
     * @return void
     */
    public function init(): void;

    public function getMapper(string $objectClass): \snuze\Persistence\Interfaces\MapperInterface;
}
