<?php

declare(strict_types=1);

namespace snuze\Request;

/**
 * UserAbout defines the structure and parameters of a request to the
 * /user/[username]/about API endpoint.
 *
 * This endpoint returns information about the specified Reddit user.
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
class UserAbout extends Request
{

    /**
     * Constructor. Calls parent, then sets properties specific to this Request.
     */
    public function __construct(string $username) {

        /* All Request children must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Validate the username so we don't send a bogus request */
        if (!preg_match(\snuze\Reddit\Thing\Account\Account::REGEX_VALID_NAME,
                        $username)) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid username');
        }

        /* Set HTTP verb */
        $this->setVerb(self::VERB_GET);

        /* Set endpoint path */
        $this->setEndpointPath("/user/{$username}/about");
    }

}
