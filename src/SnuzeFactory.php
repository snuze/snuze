<?php

declare(strict_types=1);

namespace snuze;

/**
 * The SnuzeFactory is responsible for accepting and validating configuration
 * parameters, initializing a sane environment, then creating and returning a
 * Snuze object. That's kind of a lot for a factory, but it's not really a
 * builder and it's not really just a DI container either, so "factory" it is.
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
class SnuzeFactory
{

    /**
     * The PHP extensions that must be installed/available for the Snuze core
     * components to work. A storage provider, if enabled, may define its own
     * additional required extensions.
     */
    const REQUIRED_EXTS = ['curl'];

    /**
     * An array of configuration parameters. Default values are provided, to
     * the extent possible (of course there are no default Reddit credentials).
     * Parameters passed into the constructor will override these defaults.
     *
     * @var mixed[]
     */
    private $config = [
        /* These parameters have no defaults and MUST be supplied by the user */
        'auth.client_id'     => '',
        'auth.client_secret' => '',
        'auth.username'      => '',
        'auth.password'      => '',
        'auth.user_agent'    => '',
        /* These parameters have default values */
        'autosleep'          => true,
        'log.filename'       => 'snuze.log',
        'log.label'          => '',
        'log.severity'       => 'WARNING',
        'storage.enabled'    => true,
        'storage.namespace'  => '\snuze\Persistence\SQLite',
        'storage.parameters' => ['filename' => 'snuze.sqlite'],
    ];

    /**
     * Constructor. Accepts an array of configuration parameters.
     *
     * @param array $parameters
     * @throws \UnexpectedValueException
     * @see https://snuze.shaunc.com/docs/configuration/ Config parameter docs
     */
    public function __construct(array $parameters) {

        /* Don't accept unrecognized parameters */
        foreach (array_keys($parameters) as $key) {
            if (!isset($this->config[$key])) {
                throw new \UnexpectedValueException("Unrecognized parameter '{$key}'");
            }
        }

        /* Overwrite default settings with corresponding user-supplied values */
        $this->config = array_merge($this->config, $parameters);

        /*
         * Make sure nothing was set (or left) empty. Since false can be a valid
         * value, test for nulls and empty strings.
         */
        foreach ($this->config as $key => $val) {
            if ($key === 'log.label') { /* This can be empty */
                continue;
            }
            if (($val ?? '') === '') {
                throw new \UnexpectedValueException("Parameter '{$key}' can't be empty");
            }
        }
    }

    /**
     * Build and return a configured Snuze instance.
     *
     * @return \snuze\Snuze A configured Snuze instance
     * @throws \RuntimeException
     */
    public function getSnuze(): \snuze\Snuze {

        /* Test PHP version */
        if (version_compare(phpversion(), '7.2.0', '<')) {
            throw new \RuntimeException('Snuze requires PHP 7.2 or newer, but '
                    . 'you have PHP ' . phpversion());
        }

        /* Initialize the file logger */
        $this->initializeLogger();

        /* Create an AuthenticationState */
        $auth = new AuthenticationState(
                $this->config['auth.client_id'],
                $this->config['auth.client_secret'],
                $this->config['auth.username'], $this->config['auth.password'],
                $this->config['auth.user_agent'],
                new Reddit\AccessToken($this->config['auth.username']),
                new RateLimitBucket()
        );

        /* If storage is enabled, create and initialize the StorageProvider */
        $storage = null;
        if ($this->config['storage.enabled'] === true) {
            $class = $this->config['storage.namespace'] . '\\StorageProvider';
            $storage = new $class($this->config['storage.parameters']);
            $storage->init();
            /* See if the storage provider has any mandatory PHP extensions */
            $storageExtensions = $storage->getRequiredExtensionNames();
        }

        /* Ensure that all required PHP extensions are available */
        $required = array_merge(self::REQUIRED_EXTS, $storageExtensions ?? []);
        $missing = array_diff(array_unique($required), get_loaded_extensions());
        if (!empty($missing)) {
            throw new \RuntimeException('As configured, Snuze requires the '
                    . 'following PHP extension(s): ' . join(', ', $missing)
                    . '. Please enable the extension(s) or modify the config.');
        }

        return new Snuze($auth, $storage);
    }

    /**
     * Initialize the file logger. There's no return; the logger class is a
     * singleton.
     *
     * @return void
     * @throws \UnexpectedValueException
     */
    private function initializeLogger(): void {

        \parseword\pickset\Logger::setFilename($this->config['log.filename']);

        $label = 'Snuze-' . Snuze::VERSION;
        if (!empty($this->config['log.label'])) {
            $label .= '-' . $this->config['log.label'];
        }
        \parseword\pickset\Logger::setLabel($label);

        /* Kludge; avoids user having to care about the Logger class */
        $this->config['log.severity'] = strtoupper($this->config['log.severity']);
        $severity = 'parseword\pickset\Logger::SEVERITY_' . $this->config['log.severity'];
        if (!defined($severity)) {
            throw new \UnexpectedValueException('log.severity must be one of: '
                    . "'NONE', 'ERROR', 'WARN', 'INFO', 'DEBUG'");
        }
        \parseword\pickset\Logger::setSeverityFilter(constant($severity));
    }

}
