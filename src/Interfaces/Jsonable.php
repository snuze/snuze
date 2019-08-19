<?php

namespace snuze\Interfaces;

/**
 * Jsonable promises that implementers will have public toJson() and fromJson()
 * methods to facilitate converting their objects to and from JSON strings.
 *
 * When implemented properly, it should be possible to pass the return value
 * of toJson() into fromJson() and get back a functionally identical object.
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
interface Jsonable
{

    /**
     * This method should return a JSON-formatted string that represents an
     * array of the object's significant properties.
     *
     * @return string A string containing well-formed JSON
     */
    public function toJson(): string;

    /**
     * This method should accept a JSON-formatted string, use it to construct
     * an object of the appropriate type, and return that object.
     *
     * Accurate return type declaration isn't possible yet, so the implementer
     * must take care to return the desired object type.
     *
     * @param string $json Well-formed JSON representing enough significant
     *      properties to construct an object
     * @todo Revisit this (&& overrides) once PHP 7.4 covariance is mainstream
     */
    public function fromJson(string $json);
}
