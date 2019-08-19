<?php

declare(strict_types=1);

namespace snuze\Persistence\SQLite;

use snuze\{
    Persistence\Interfaces\AccessTokenMapperInterface,
    Persistence\Interfaces\StorageProviderInterface,
    Persistence\SQLite\StorageProvider,
    Reddit\AccessToken
};

/**
 * A data mapper class for AccessToken objects using the SQLite StorageProvider.
 *
 * This is where abstraction goes to die.
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
class AccessTokenMapper extends \snuze\SnuzeObject implements AccessTokenMapperInterface
{

    /**
     * The SQLite Mapper implementations don't care about the entire storage
     * provider object, we just need a PDO handle
     *
     * @var \PDO
     */
    private $pdo = null;

    /**
     *
     * @param \snuze\Persistence\SQLite\StorageProvider $storage A
     *      SQLite StorageProvider object
     * @throws \snuze\Exception\ArgumentException
     */
    public function __construct(StorageProviderInterface $storage) {
        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Test that the storage provider is of the expected type */
        if (!$storage instanceof StorageProvider) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A ' . StorageProvider::class . ' object must be supplied');
        }

        /*
         * Set the local PDO property; the SQLite Mapper implementations don't
         * use anything else from the StorageProvider
         */
        $this->pdo = $storage->getPdo();
    }

    /**
     * Insert or update an AccessToken to the SQLite database.
     *
     * @param \snuze\Reddit\AccessToken $accessToken The AccessToken
     *      to persist
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function persist($accessToken): bool {

        /* This mapper only knows how to persist one thing */
        if (!$accessToken instanceof AccessToken) {
            throw new \snuze\Exception\ArgumentException($this,
                    'An AccessToken object must be supplied');
        }

        $this->debug('Persisting AccessToken ' . $accessToken->_ident());

        /* Ensure the object is sufficiently populated to persist */
        foreach (['token', 'tokenType', 'username', 'scope', 'expires'] as
                    $property) {
            $fn = 'get' . $property;
            if (empty($accessToken->$fn())) {
                throw new \snuze\Exception\PersistenceException($this,
                        "Can't store AccessToken with empty property: {$property}");
            }
        }

        /* Attempt to save this AccessToken to the database */
        $query = <<<EOT
                INSERT OR IGNORE INTO access_tokens
                (username, access_token, expires, scope, token_type)
                VALUES
                (:username, :access_token, :expires, :scope, :token_type)
EOT;
        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $accessToken->getUsername(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':access_token', $accessToken->getToken(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':expires', $accessToken->getExpires(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':scope', $accessToken->getScope(), \PDO::PARAM_STR);
            $stmt->bindValue(':token_type', $accessToken->getTokenType(),
                    \PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        return true;
    }

    /**
     * Get an AccessToken from the database by its `token` field.
     *
     * @param string $key The `token` field of the AccessToken to retrieve. It's
     *      the only unique property of an AccessToken.
     * @return \snuze\Reddit\AccessToken|null
     */
    public function retrieve($key) {

        $query = <<<EOT
            SELECT username
                ,access_token
                ,expires - STRFTIME('%s', 'now') AS expires_in
                ,scope
                ,token_type
            FROM access_tokens
            WHERE token = :token
EOT;

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':token', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("Requested token {$id} wasn't found");
            return null;
        }

        /* Return an AccessToken object */
        return (new AccessToken($row['username']))->fromJson(json_encode($row),
                        JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

    /**
     * Get the newest unexpired access token for the specified user, if one
     * exists.
     *
     * @param string $username The username for whom to retrieve a token
     * @return \snuze\Reddit\AccessToken|null An AccessToken object,
     *      or null if there was no matching row
     * @throws \snuze\Exception\PersistenceException
     */
    public function retrieveOneFor(string $username): ?\snuze\Reddit\AccessToken {

        $query = <<<EOT
            SELECT username
                ,access_token
                ,expires - STRFTIME('%s', 'now') AS expires_in
                ,scope
                ,token_type
            FROM access_tokens
            WHERE username = :username
            AND expires > :imminent
            ORDER BY expires DESC
            LIMIT 1
EOT;

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
            $stmt->bindValue(':imminent', time() + 60, \PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("No valid unexpired token found for {$username}");
            return null;
        }

        /* Return an AccessToken object */
        return (new AccessToken($username))->fromJson(json_encode($row),
                        JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

    /**
     *
     * @param \snuze\Reddit\AccessToken $object The AccessToken
     *      to delete
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function delete($object): bool {

        /* This mapper only knows how to delete one thing */
        if (!$object instanceof AccessToken) {
            throw new \snuze\Exception\ArgumentException($this,
                    'An AccessToken object must be supplied');
        }

        $query = 'DELETE FROM access_tokens WHERE access_token = :token';

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':token', $object->getToken(), \PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        return true;
    }

    /**
     * Delete expired AccessTokens from the database. This is called by Snuze
     * prior to persisting a new token.
     *
     * @param int $olderThan Tokens that expired more than this many seconds ago
     *      will be deleted. Defaults to 1 day.
     * @throws \snuze\Exception\PersistenceException
     */
    public function purge(int $olderThan = 86400) {

        $query = 'DELETE FROM access_tokens WHERE expires < :expires';

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':expires', time() - $olderThan, \PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }
    }

}
