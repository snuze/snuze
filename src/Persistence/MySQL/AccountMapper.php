<?php

declare(strict_types=1);

namespace snuze\Persistence\MySQL;

use snuze\{
    Persistence\Interfaces\AccountMapperInterface,
    Persistence\Interfaces\StorageProviderInterface,
    Persistence\MySQL\StorageProvider,
    Reddit\Thing\Account\Account,
    Reddit\Thing\Account\MyAccount,
    Reddit\Thing\Account\UserAccount
};

/**
 * A data mapper class for Account subtype objects using the MySQL StorageProvider.
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
class AccountMapper extends \snuze\SnuzeObject implements AccountMapperInterface
{

    /**
     * A place to stash the PDO connection from the StorageProvider we're given
     *
     * @var \PDO
     */
    private $pdo = null;

    /**
     *
     * @param \snuze\Persistence\MySQL\StorageProvider $storage A
     *      MySQL StorageProvider object
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
         * Set the local PDO property; the MySQL Mapper implementations don't
         * use anything else from the StorageProvider
         */
        $this->pdo = $storage->getPdo();
    }

    /**
     * Insert or update an Account subtype to the MySQL database.
     *
     * If a MyAccount object is supplied, only the properties associated with
     * a UserAccount are persisted; attributes that are specific to MyAccount
     * won't be stored.
     *
     * @param \snuze\Reddit\Thing\Account\Account $account The account
     *      to persist
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function persist($account): bool {

        /* This mapper only knows how to persist one thing */
        if (!$account instanceof Account) {
            throw new \snuze\Exception\ArgumentException($this,
                    'An Account subtype object must be supplied');
        }
        $this->debug('Persisting Account ' . $account->getName());

        /* Ensure the object is sufficiently populated to persist */
        foreach (['id', 'name'] as $property) {
            $fn = 'get' . $property;
            if (empty($account->$fn())) {
                throw new \snuze\Exception\PersistenceException($this,
                        "Can't store Account with empty property: {$property}");
            }
        }

        /* Attempt to save this Account to the MySQL database */
        $query = <<<EOT
                INSERT INTO accounts (
                    id
                    ,created
                    ,created_utc
                    ,comment_karma
                    ,has_subscribed
                    ,has_verified_email
                    ,hide_from_robots
                    ,icon_img
                    ,is_employee
                    ,is_friend
                    ,is_gold
                    ,is_mod
                    ,link_karma
                    ,name
                    ,pref_show_snoovatar
                    ,subreddit
                    ,verified
                )
                VALUES (
                    :id
                    ,:created
                    ,:created_utc
                    ,:comment_karma
                    ,:has_subscribed
                    ,:has_verified_email
                    ,:hide_from_robots
                    ,:icon_img
                    ,:is_employee
                    ,:is_friend
                    ,:is_gold
                    ,:is_mod
                    ,:link_karma
                    ,:name
                    ,:pref_show_snoovatar
                    ,:subreddit
                    ,:verified
                )
                ON DUPLICATE KEY UPDATE
                    created = VALUES(created)
                    ,created_utc = VALUES(created_utc)
                    ,comment_karma = VALUES(comment_karma)
                    ,has_subscribed = VALUES(has_subscribed)
                    ,has_verified_email = VALUES(has_verified_email)
                    ,hide_from_robots = VALUES(hide_from_robots)
                    ,icon_img = VALUES(icon_img)
                    ,is_employee = VALUES(is_employee)
                    ,is_friend = VALUES(is_friend)
                    ,is_gold = VALUES(is_gold)
                    ,is_mod = VALUES(is_mod)
                    ,link_karma = VALUES(link_karma)
                    ,name = VALUES(name)
                    ,pref_show_snoovatar = VALUES(pref_show_snoovatar)
                    ,subreddit = VALUES(subreddit)
                    ,verified = VALUES(verified)
EOT;
        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $account->getId(), \PDO::PARAM_STR);
            $stmt->bindValue(':created', (int) $account->getCreated(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':created_utc', (int) $account->getCreatedUtc(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':comment_karma', $account->getCommentKarma(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':has_subscribed', $account->getHasSubscribed(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':has_verified_email',
                    $account->getHasVerifiedEmail(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':hide_from_robots', $account->getHideFromRobots(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':icon_img', $account->getIconImg(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':is_employee', $account->getIsEmployee(),
                    \PDO::PARAM_BOOL);
            $isFriend = ($account instanceof MyAccount) ? true : $account->getIsFriend();
            $stmt->bindValue(':is_friend', $isFriend, \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_gold', $account->getIsGold(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_mod', $account->getIsMod(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':link_karma', $account->getLinkKarma(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':name', $account->getName(), \PDO::PARAM_STR);
            $stmt->bindValue(':pref_show_snoovatar',
                    $account->getPrefShowSnoovatar(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':subreddit',
                    json_encode($account->getSubreddit(),
                            JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':verified', $account->getVerified(),
                    \PDO::PARAM_BOOL);

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
     * Get an account from the database by its username.
     *
     * @param string $username The username of the account to retrieve
     * @return \snuze\Reddit\Thing\Account\UserAccount|null
     */
    public function retrieve($username) {

        $query = <<<EOT
            SELECT * -- meh
            FROM accounts
            WHERE name = :username
EOT;

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("Requested account {$username} wasn't found");
            return null;
        }

        /**
         * Boolean fields are stored as BIT (0 or 1) in the database. Snuze
         * expects booleans, and uses strict typing. Here the 0s and 1s are
         * massaged back to boolean values.
         */
        $boolFields = [
            'has_subscribed',
            'has_verified_email',
            'hide_from_robots',
            'is_employee',
            'is_friend',
            'is_gold',
            'is_mod',
            'pref_show_snoovatar',
            'verified'
        ];
        foreach ($boolFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = (bool) $row[$field];
            }
        }

        /**
         * Array fields are stored in the database as JSON-encoded strings. Here
         * they're massaged back into arrays so json_encode() (below) doesn't
         * double-encode them.
         */
        $arrayFields = [
            'subreddit'
        ];
        foreach ($arrayFields as $field) {
            if (!empty($row[$field])) {
                $row[$field] = json_decode($row[$field], true);
            }
        }

        /**
         * Reddit provides epoch timestamps as floats. They're stored in the
         * database as integers. Here they're massaged back into floats.
         */
        foreach (['created', 'created_utc'] as $field) {
            $row[$field] = (float) ($row[$field]);
        }

        /* Finally, wrap it all up in a Reddit "thing" style JSON package */
        $arr = [
            'kind' => 't2',
            'data' => $row
        ];

        /* Return a UserAccount object */
        return (new UserAccount())->fromJson(json_encode($arr,
                                JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
    }

    /**
     * Delete an account from the database.
     *
     * @param \snuze\Reddit\Thing\Account\Account $object The account to delete
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function delete($object): bool {

        /* This mapper only knows how to delete one thing */
        if (!$object instanceof Account) {
            throw new \snuze\Exception\ArgumentException($this,
                    'An Account subtype object must be supplied');
        }
        $this->debug('Deleting account ' . $object->getName());

        $query = 'DELETE FROM accounts WHERE id = :id';

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $object->getId(), \PDO::PARAM_STR);
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
     * Get an account from the database by its Reddit internal ID. This incurs
     * an extra query penalty, as it first looks up the corresponding username
     * and then calls retrieve() to do the heavy lifting.
     *
     * @param string $id The Reddit internal ID (e.g. "bva2") of the account
     *      to retrieve
     * @return \snuze\Reddit\Thing\Account\UserAccount|null
     * @throws \snuze\Exception\PersistenceException
     */
    public function retrieveById(string $id): ?\snuze\Reddit\Thing\Account\UserAccount {

        $query = 'SELECT name FROM accounts WHERE id = :id';

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("Requested account {$id} wasn't found");
            return null;
        }

        return $this->retrieve($row['name']);
    }

}
