<?php

declare(strict_types=1);

namespace snuze\Request\Links;

/**
 * TopLinks defines the structure and parameters of a request to the
 * /r/[subreddit]/top API endpoint.
 *
 * This endpoint returns links from the specified subreddit, presented in the
 * "top" sort order.
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
class TopLinks extends Links
{

    /**
     * Defines the "t" parameter as valid
     *
     * A timeframe restriction. This is ONLY a valid parameter when the sort
     * order is SORT_TOP or SORT_CONTROVERSIAL, and is required in those cases.
     */
    const PARAM_TIME = 't';

    public function __construct(string $subredditName) {

        /* All Links children must call parent ctor */
        parent::__construct($subredditName, Links::SORT_TOP);
        $this->debug('ctor args: ' . var_export(func_get_args(), true));


        /* Set mandatory parameters for this Request type */
        $this->mandatoryParameters = [
            self::PARAM_TIME,
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

        /* PARAM_TIME must be set to one of several predefined values */
        if (!in_array($this->getParameter(self::PARAM_TIME),
                        ['hour', 'day', 'week', 'month', 'year', 'all'])) {
            throw new \snuze\Exception\ArgumentException($this,
                    "Unsupported value '{$this->getParameter(self::PARAM_TIME)}' "
                    . 'for parameter: ' . self::PARAM_TIME);
        }

        return true;
    }

}
