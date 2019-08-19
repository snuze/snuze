<?php

declare(strict_types=1);

namespace snuze\Persistence\SQLite;

use \snuze\Persistence\Interfaces\StorageProviderInterface;

/**
 * This is the SQLite storage provider class. It provides the very basic
 * functionality required for Snuze to persist AccessTokens to a data file.
 *
 * If you want to develop your own Snuze storage provider, you should look
 * at the included MySQL storage provider instead. This one doesn't do enough
 * to be a useful example.
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
    const REQUIRED_EXTS = ['pdo_sqlite'];

    /**
     * The filename of the target SQLite database file
     *
     * @var string
     */
    private $filename = null;

    /**
     * A PDO handle to the SQLite data file
     *
     * @var \PDO
     */
    private $pdo = null;

    /**
     * Creates a new SQLite\StorageProvider object.
     *
     * You must pass an array with a single element whose key is 'filename'
     * and whose value is the path to the target SQLite data file. If that
     * file doesn't exist, it will be created when init() is called.
     *
     * @param string[] $configurationParameters An array with an element named
     *      'filename' pointing to the target data file
     * @throws \snuze\Exception\ArgumentException
     */
    public function __construct(array $configurationParameters = []) {
        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Test that the filename was supplied */
        if (empty($configurationParameters['filename'])) {
            throw new \snuze\Exception\ArgumentException($this,
                    "A 'filename' parameter is required");
        }
        $this->filename = $configurationParameters['filename'];
    }

    /**
     * Open a PDO handle to the data file, and make sure the access_tokens
     * table exists. If the table isn't there, create it.
     *
     * @return void
     * @throws \snuze\Exception\PersistenceException
     */
    public function init(): void {

        /* Initialize the PDO handle */
        try {
            $this->pdo = new \PDO('sqlite:' . $this->filename);
        }
        catch (\PDOException $ex) {
            /* Re-throw as a logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        /* PDO should treat all errors as exceptions */
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        /* Ensure the access_tokens table exists; if not, create it */
        if (!$this->validateSchema()) {
            $this->createSchema();
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
     * Test for the presence of an expected table in the data file. Called
     * by init().
     *
     * @return bool
     */
    private function validateSchema(): bool {

        /* Verify that the expected table exists */
        try {
            $result = $this->pdo->query('SELECT COUNT(1) FROM access_tokens');
        }
        catch (\PDOException $ex) {
            /* Swallow "no such table" exception so createSchema() is called */
            if (strpos($ex->getMessage(), 'no such table') !== false) {
                return false;
            }
            /* Re-throw anything else as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($result)) {
            $this->info("Database schema doesn't seem to exist");
            return false;
        }

        return true;
    }

    /**
     * Create the expected schema. Called by init() if validateSchema() returns
     * false.
     *
     * @return bool
     * @throws \snuze\Exception\PersistenceException
     */
    private function createSchema(): bool {

        /* Create the access_tokens table */
        $this->info('Creating access_tokens table');
        $query = <<<EOT
                    CREATE TABLE IF NOT EXISTS access_tokens (
                        username VARCHAR(32) NOT NULL,
                        access_token VARCHAR(64) NOT NULL,
                        expires INTEGER NOT NULL,
                        scope VARCHAR(255) NOT NULL,
                        token_type VARCHAR(12) NOT NULL DEFAULT 'bearer',
                        refresh_token VARCHAR(32) NULL,
                        PRIMARY KEY(username, access_token, expires)
                    );
EOT;
        try {
            $this->pdo->exec($query);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        return true;
    }

}
