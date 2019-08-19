<?php

declare(strict_types=1);

namespace snuze\Persistence;

use \snuze\Persistence\Interfaces\StorageProviderInterface;

/**
 * The AbstractStorageProvider contains basic implementations for two of the
 * methods required by StorageProviderInterface; namely, getMapper() and
 * getRequiredExtensionNames(). Depending on the type of storage involved,
 * these "stubs" are likely capable of standing on their own, and probably
 * don't need to be overridden.
 *
 * If you want to develop your own Snuze storage provider, it will need to
 * extend this class, implementing init() at the very least. Have a look at
 * the MySQL storage provider to see an example.
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
abstract class AbstractStorageProvider extends \snuze\SnuzeObject implements StorageProviderInterface
{

    /**
     * An array of Mapper subtype objects. It's wasteful to create multiple
     * Mappers of the same type in a given execution context, so Mappers will
     * be cached here when the storage provider creates them.
     *
     * @var \snuze\Persistence\Interfaces\MapperInterface[]
     */
    protected static $mappers = [];

    /**
     * Given the short class name of an object (e.g. 'AccessToken' or 'Link'),
     * return a corresponding Mapper to handle that object, as implemented
     * by this particular storage provider. If this storage provider hasn't
     * implemented a Mapper for the requested type, an exception is thrown.
     *
     * @param string $objectClass The short class name of the object a Mapper is
     *      desired for, e.g. 'Subreddit' or 'Link'
     * @return \snuze\Persistence\Interfaces\MapperInterface A Mapper
     *      for the requested object type
     * @throws \snuze\Exception\PersistenceException
     */
    public function getMapper(string $objectClass): \snuze\Persistence\Interfaces\MapperInterface {

        /* Create a Mapper of this type if one isn't already in the cache */
        if (empty(self::$mappers[$objectClass])) {
            $namespace = substr(static::class, 0, strrpos(static::class, '\\'));
            $class = "{$namespace}\\{$objectClass}Mapper";
            try {
                self::$mappers[$objectClass] = new $class($this);
            }
            catch (\Throwable $ex) {
                /* Format "Class...not found" with a nicer message */
                $message = $ex->getMessage();
                if (strpos($message, 'not found') !== false) {
                    $message = "This storage provider hasn't implemented a "
                            . "Mapper for {$objectClass} objects.";
                }
                /* Re-throw as logged SnuzeException */
                throw new \snuze\Exception\PersistenceException($this, $message,
                        $ex->getCode(), $ex->getPrevious());
            }
        }

        /* Return the Mapper */
        return self::$mappers[$objectClass];
    }

    /**
     * Return the array of extensions this provider requires in order to
     * operate properly.
     *
     * @return array
     */
    public function getRequiredExtensionNames(): array {
        if (!defined(static::class . '::REQUIRED_EXTS')) {
            throw new \snuze\Exception\RuntimeException($this,
                    "This storage provider hasn't defined the REQUIRED_EXTS constant");
        }
        return static::REQUIRED_EXTS;
    }

    /**
     * Your storage provider must implement this method to perform whatever
     * actions are necessary to initialize it; for example, opening a PDO
     * connection, making sure certain data tables exist, etc.
     *
     * @return void
     */
    abstract public function init(): void;
}
