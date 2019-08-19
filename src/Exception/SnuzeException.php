<?php

namespace snuze\Exception;

/**
 * This custom exception accepts a SnuzeObject as its first argument, allowing
 * the caller to inject itself. Because every SnuzeObject has an internal ID,
 * this provides for extra tracing information to be automatically prepended
 * to the exception message (and, ultimately, the log file).
 *
 * Additionally, the $code parameter is forced to an integer type.
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
class SnuzeException extends \parseword\pickset\Exception\LoggedException
{

    /**
     * {@inheritDoc}
     *
     * @param \snuze\SnuzeObject|null $snuzeObject If the caller is a
     *      subtype of SnuzeObject, pass its $this reference; otherwise null
     */
    public function __construct(\snuze\SnuzeObject $snuzeObject = null,
            $message = '', $code = 0, \Throwable $previous = null) {

        /* $code should be an int */
        if (!empty($code) && !is_int($code)) {
            $code = (int) preg_replace('|^\d|', '', $code);
        }

        /* Invoke parent, with extra Snuze debug details where applicable */
        $extra = '';
        if ($snuzeObject instanceof \snuze\SnuzeObject) {
            $extra = "In {$snuzeObject->_ident()} ";
        }
        parent::__construct($extra . $message, $code, $previous);
    }

}
