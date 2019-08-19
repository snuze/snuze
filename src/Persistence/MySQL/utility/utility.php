<?php

/**
 * This is the Snuze MySQL storage provider database utility. It installs the
 * Snuze MySQL schema and applies updates as they become available. The utility
 * should be run when enabling the MySQL storage provider for the first time,
 * and also when upgrading Snuze to a version that contains database changes.
 *
 * Snuze will prompt you to run this utility whenever it's needed.
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
declare(strict_types=1);

if (!empty($_SERVER['REQUEST_METHOD']) || !empty($_SERVER['REMOTE_ADDR'])) {
    die('This script must be run from the command line.' . PHP_EOL);
}

define('SCHEMA_DIR', __DIR__ . '/../schema/');

/* Ask the user for the MySQL connection parameters */
$db = prompt_user_for_credentials();

/* Get a PDO connection */
echo PHP_EOL . 'Connecting to MySQL...' . PHP_EOL;
$pdo = get_pdo($db);

/* Get the current schema version, if any */
$version = get_schema_version($pdo);

/* Does anything need doin'? */
if (empty($version)) {

    /* The schema needs to be created from scratch */
    echo 'No existing schema was found. The tables need to be created.' . PHP_EOL;

    /* Confirm the user wants to install the schema */
    if (!prompt_user_for_permission()) {
        echo 'Aborting! No changes were made.' . PHP_EOL;
        exit;
    }

    /* Load the base schema file and run statements to create the schema */
    if (execute_statements_from_file($pdo, 'snuze.sql')) {
        echo 'Schema installation is complete!' . PHP_EOL;
    }
    exit;
}

/* Find patch files higher than the current schema version */
echo "Installed schema is version {$version}" . PHP_EOL;
echo 'Looking for applicable patches...' . PHP_EOL . PHP_EOL;

$patches = find_patch_files($version);

if (empty($patches)) {
    echo 'No applicable patch files were found. Nothing to do, exiting.' . PHP_EOL;
    exit;
}
echo 'Patch file(s) located: ' . PHP_EOL;
foreach ($patches as $patch) {
    echo "\t{$patch}" . PHP_EOL;
}

/* Confirm the user wants to apply the patch file(s) */
if (!prompt_user_for_permission()) {
    echo 'Aborting! No changes were made.' . PHP_EOL;
    exit;
}

/* Apply the patch files */
foreach ($patches as $patch) {
    echo "Applying patch {$patch}..." . PHP_EOL;
    execute_statements_from_file($pdo, $patch);
}
echo 'Schema upgrade is complete!' . PHP_EOL;
exit;

/**
 * Ask the user to provide their MySQL database settings and return the info
 * as an array.
 *
 * @return array
 */
function prompt_user_for_credentials(): array {
    $db = [
        'host' => null,
        'user' => null,
        'pass' => null,
        'name' => null,
    ];

    /* Prompt for database credentials */
    echo <<<EOT

This is the Snuze MySQL storage provider database utility. Please follow along
with the prompts to upgrade or install your Snuze MySQL database.


EOT;

    while (1) {
        echo 'Enter MySQL hostname or IP address: ';
        $input = trim(fgets(STDIN));
        if (empty($input)) {
            echo "Try again, or ctrl-c to quit." . PHP_EOL;
            continue;
        }
        $db['host'] = $input;
        break;
    }
    while (1) {
        echo 'Enter MySQL username: ';
        $input = trim(fgets(STDIN));
        if (empty($input)) {
            echo "Try again, or ctrl-c to quit." . PHP_EOL;
            continue;
        }
        $db['user'] = $input;
        break;
    }
    while (1) {
        echo 'Enter MySQL password: ';
        $input = trim(fgets(STDIN));
        if (empty($input)) {
            echo "Try again, or ctrl-c to quit." . PHP_EOL;
            continue;
        }
        $db['pass'] = $input;
        break;
    }
    while (1) {
        echo 'Enter MySQL database name (must exist already): ';
        $input = trim(fgets(STDIN));
        if (empty($input)) {
            echo "Try again, or ctrl-c to quit." . PHP_EOL;
            continue;
        }
        $db['name'] = $input;
        break;
    }

    return $db;
}

/**
 * Ask the user for permission before making changes.
 *
 * @return bool
 */
function prompt_user_for_permission(): bool {
    echo PHP_EOL;
    while (1) {
        echo 'Apply these changes now? [yes/no]: ';
        $input = trim(fgets(STDIN));
        if (!in_array(strtolower($input), ['yes', 'no'])) {
            echo "Try again, or ctrl-c to quit." . PHP_EOL;
            continue;
        }
        break;
    }
    return strtolower($input) === 'yes';
}

/**
 * Get a PDO connection using the given MySQL parameters
 *
 * @param type $db An array with elements 'host', 'user', 'pass', and 'name'
 * @return \PDO A PDO connection handle
 */
function get_pdo(array $db): \PDO {
    $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4";
    $pdo = new \PDO($dsn, $db['user'], $db['pass']);

    /* PDO should treat all errors as exceptions */
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    /* Get ints as ints, floats as floats, etc. */
    $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

    /* Be strict about truncation, range errors, and other problems */
    $pdo->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");

    return $pdo;
}

/**
 * Query the database for the currently installed schema version, if any.
 *
 * @param \PDO $pdo A PDO connection
 * @return int The schema version, or 0 if no schema exists.
 * @throws \PDOException
 */
function get_schema_version(\PDO $pdo): int {
    $version = null;
    try {
        $version = $pdo->query('SELECT schema_version FROM snuze')->fetchColumn(0);
    }
    catch (\PDOException $ex) {
        /* Swallow "table or view not found" and re-throw anything else */
        if (strpos($ex->getMessage(), 'table or view not found') === false) {
            throw $ex;
        }
    }
    return (int) $version;
}

/**
 * Look in Snuze's src\Persistence\MySQL\schema directory for patch files whose
 * names contain a version number higher than $version
 *
 * @param int $version Patch files named for this version or earlier will be ignored
 * @return string[] An array of filenames (no paths)
 */
function find_patch_files(int $version): array {
    $files = [];
    foreach (scandir(SCHEMA_DIR) as $file) {
        if (preg_match('|^patch-(\d+)\.sql$|', $file, $match) && $match[1] > $version) {
            $files[] = $file;
        }
    }
    return $files;
}

/**
 * Given the name of a patch file containing SQL statements separated by '--',
 * get the file's contents, split into individual statements, and execute each one.
 *
 * @param \PDO $pdo A PDO connection
 * @param string $filename A patch file name
 * @return bool True on success. PDO may throw a \PDOException. If $filename
 *      isn't found, execution will terminate.
 */
function execute_statements_from_file(\PDO $pdo, string $filename): bool {

    $path = SCHEMA_DIR . '/' . $filename;
    if (!file_exists($path)) {
        echo "File {$path} wasn't found! Aborting." . PHP_EOL;
        exit;
    }

    /* Load the file, break it into individual statements, and run each one */
    $sql = file_get_contents($path);

    foreach (explode('--', $sql) as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }

    return true;
}
