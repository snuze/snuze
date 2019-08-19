<?php

declare(strict_types=1);

namespace snuze\Request;

/**
 * Info defines the structure and parameters of a request to the
 * /api/info API endpoint.
 *
 * This endpoint returns information about up to 100 different "thing" entities
 * as specified by their fullname identifiers.
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
class Info extends Request
{

    /**
     * Defines the "id" parameter as valid. This parameter is mandatory and
     * must be a Thing's fullname e.g. "t3_ce4vtr" or multiple comma-separated
     * Thing fullnames e.g. "t5_abc,t1_def,t3_ghi". Only comments, links, and
     * subreddits (t1_, t3_, and t5) can be retrieved through this Request.
     */
    const PARAM_ID = 'id';

    /**
     * Defines a regular expression to test whether or not the "id" parameter's
     * value is valid
     */
    const REGEX_VALID_ID = '|^(?:(?:t[135]_[a-z0-9]{1,13}),?)+$|i';

    /**
     * Constructor. Calls parent, then sets properties specific to this Request.
     */
    public function __construct() {

        /* All Request children must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Set HTTP verb */
        $this->setVerb(self::VERB_GET);

        /* Set endpoint path */
        $this->setEndpointPath('/api/info');

        /* Set mandatory parameters for this Request type */
        $this->mandatoryParameters = [
            self::PARAM_ID,
        ];
    }

    /**
     * Overrides and calls parent. Performs parameter validation specific to
     * this type of Request.
     *
     * @return bool Whether or not the Request is ready to send to the API
     * @throws \snuze\Exception\ArgumentException
     */
    public function validate(): bool {

        parent::validate();

        /* Check the value of the ID parameter. */
        if (!preg_match(self::REGEX_VALID_ID,
                        $this->getParameter(self::PARAM_ID))) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Unsupported value for parameter ' . self::PARAM_ID
                    . '. Only link/comment/subreddit fullnames permitted here');
        }

        return true;
    }

}
