<?php

declare(strict_types=1);

namespace snuze\Persistence\MySQL;

use \snuze\Persistence\Interfaces\StorageProviderInterface;

/**
 * This is the MySQL storage provider class. It provides the functionality
 * required for Snuze to talk to a MySQL database via PDO.
 *
 * If you want to develop your own Snuze storage provider, looking here is a
 * good place to start. Your storage provider needs to:
 *
 *  * Extend AbstractStorageProvider
 *  * Implement StorageProviderInterface and its methods
 *  * Establish communication with, write to, and read from the storage facility
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
class StorageProvider extends \snuze\Persistence\AbstractStorageProvider implements StorageProviderInterface
{

    /**
     * PHP extensions required by this storage provider
     */
    const REQUIRED_EXTS = ['pdo_mysql'];

    /**
     * The directory containing .sql DDL files that define the base MySQL
     * schema and the inter-version patches.
     *
     * This is specific to the MySQL storage provider. If implementing your
     * own storage provider, you're welcome to mirror this design, but it's not
     * a required feature of a Snuze storage provider in general.
     */
    const SCHEMA_DIR = __DIR__ . '/schema';

    /**
     * The schema version number that this version of the MySQL storage provider
     * expects to be working with. This will always be the build number of Snuze
     * when schema changes were last made.
     *
     * This is specific to the MySQL storage provider. If implementing your
     * own storage provider, you're welcome to mirror this design, but it's not
     * a required feature of a Snuze storage provider in general.
     */
    const SCHEMA_VERSION = 1000800;

    /**
     * The MySQL server hostname or IP address
     *
     * @var string
     */
    private $server = null;

    /**
     * The MySQL server username
     *
     * @var string
     */
    private $username = null;

    /**
     * The MySQL server password
     *
     * @var string
     */
    private $password = null;

    /**
     * The MySQL database name
     *
     * @var string
     */
    private $database = null;

    /**
     * A PDO handle to the MySQL database
     *
     * @var \PDO
     */
    private $pdo = null;

    /**
     * Creates a new MySQL\StorageProvider object.
     *
     * You must pass an array with four elements whose keys are 'server',
     * 'username', 'password', and 'database', and whose values will be used
     * to establish a PDO connection to MySQL.
     *
     * @param string[] $configurationParameters An array with elements named
     *      'server', 'username', 'password', and 'database'
     * @throws \snuze\Exception\ArgumentException
     */
    public function __construct(array $configurationParameters = []) {
        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Test that the correct parameters were supplied */
        foreach (['server', 'username', 'password', 'database'] as $key) {
            if (empty($configurationParameters[$key])) {
                throw new \snuze\Exception\ArgumentException($this,
                        "A '{$key}' parameter is required");
            }
        }
        $this->server = $configurationParameters['server'];
        $this->username = $configurationParameters['username'];
        $this->password = $configurationParameters['password'];
        $this->database = $configurationParameters['database'];
    }

    /**
     * Open a PDO handle to the database and test for the expected schema
     * version. If the schema is outdated or doesn't exist yet, the user is
     * prompted to run the included utility script, which will install or
     * upgrade the schema as needed.
     *
     * @return void
     * @throws \snuze\Exception\PersistenceException
     */
    public function init(): void {

        /* Initialize the PDO handle */
        $dsn = "mysql:host={$this->server};dbname={$this->database};charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $this->username, $this->password);

            /* PDO should treat all errors as exceptions */
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            /* Get ints as ints, floats as floats, etc. */
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

            /* Be strict about truncation, range errors, and other problems */
            $this->pdo->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
        }
        catch (\PDOException $ex) {
            /* Re-throw as a logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        /* Test for schema sanity and alert the user if there's a problem */
        if (!$this->validateSchema()) {
            throw new \snuze\Exception\PersistenceException($this,
                    'The MySQL database schema needs to be installed or updated. '
                    . 'For more information, please read ' . __DIR__
                    . '/utility/README.txt');
        }
    }

    /**
     * Get the PDO object
     *
     * @return \PDO
     */
    public function getPdo(): \PDO {
        return $this->pdo;
    }

    /**
     * Called by init().
     *
     * Test for the presence of the "snuze" table in the database, and that its
     * "schema_version" field matches self::SCHEMA_VERSION. If this fails, ask
     * the user to go run the utility script to either install or upgrade the
     * database schema.
     *
     * This is specific to the MySQL storage provider. If implementing your
     * own storage provider, you're welcome to mirror this design, but it's not
     * a required feature of a Snuze storage provider in general.
     *
     * @return bool
     */
    private function validateSchema(): bool {

        /* Query for the schema version */
        try {
            $version = $this->pdo->query('SELECT MAX(schema_version) FROM snuze')
                    ->fetchColumn(0);
        }
        catch (\PDOException $ex) {
            /* Swallow "table or view not found" exception to return false */
            if (strpos($ex->getMessage(), 'table or view not found') !== false) {
                return false;
            }
            /* Re-throw anything else as a logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        /* Test the version */
        if (empty($version) || $version < self::SCHEMA_VERSION) {
            return false;
        }

        return true;
    }

}
