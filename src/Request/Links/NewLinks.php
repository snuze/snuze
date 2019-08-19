<?php

declare(strict_types=1);

namespace snuze\Request\Links;

/**
 * NewLinks defines the structure and parameters of a request to the
 * /r/[subreddit]/new API endpoint.
 *
 * This endpoint returns links from the specified subreddit, presented in the
 * "new" sort order.
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
class NewLinks extends Links
{

    public function __construct(string $subredditName) {

        /* All Links children must call parent ctor */
        parent::__construct($subredditName, Links::SORT_NEW);
        $this->debug('ctor args: ' . var_export(func_get_args(), true));
    }

}
