<?php

declare(strict_types=1);

namespace snuze;

use parseword\pickset\{
    Logger,
    TextUtils
};

/**
 * SnuzeObject is the ancestor class for almost every object in the Snuze
 * package. It sets a unique internal identifier for each instance of each
 * class, to assist with tracing log messages; and it exposes four logging
 * methods (debug(), error(), info(), and warning()) that are common to
 * all descendants.
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
class SnuzeObject
{

    /**
     * A randomly-generated internal ID for this object, used for logging.
     *
     * @var string
     */
    protected $_snuzeId = null;

    /**
     * The object's short class name, without namespace paths, used for logging.
     *
     * @var string
     */
    protected $_snuzeClass = null;

    public function __construct(array $args = null) {

        /* Generate a unique internal identifier */
        $this->_snuzeId = date('Ymd-His-') . bin2hex(random_bytes(8));

        /* Set the short class name */
        $this->_snuzeClass = substr(strrchr(get_class($this), '\\'), 1);
    }

    public function _getSnuzeId(): string {
        return $this->_snuzeId;
    }

    public function _getSnuzeClass(): string {
        return $this->_snuzeClass;
    }

    public function _ident(): string {
        return "{$this->_getSnuzeClass()} ID {$this->_getSnuzeId()}";
    }

    protected function debug(string $message, bool $echo = false): void {
        /* Include memory stats in debug messages */
        $ram = TextUtils::bytesToHuman(memory_get_usage(true))
                . '/'
                . TextUtils::bytesToHuman(memory_get_peak_usage(true));

        Logger::debug("({$ram}) {$this->_ident()} {$message}", $echo);
    }

    protected function info(string $message, bool $echo = false): void {
        Logger::info("{$this->_ident()} {$message}", $echo);
    }

    protected function warning(string $message, bool $echo = false): void {
        Logger::warning("{$this->_ident()} {$message}", $echo);
    }

    protected function error(string $message, bool $echo = false): void {
        Logger::error("{$this->_ident()} {$message}", $echo);
    }

}
