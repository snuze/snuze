<?php

declare(strict_types=1);

namespace snuze;

/**
 * The AuthenticationState object serves as a container for:
 *
 * - The Reddit account and client credentials being used to access the API
 * - An AccessToken object to hold authentication data, and
 * - A RateLimitBucket to track the current state of the API rate limiter.
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
class AuthenticationState extends SnuzeObject
{

    /**
     * The unique client ID for the script application. This must be assigned
     * by Reddit.
     *
     * @var string
     * @see https://github.com/reddit-archive/reddit/wiki/OAuth2#getting-started
     */
    public $clientId = '';

    /**
     * The client secret for the script application. This must be assigned
     * by Reddit.
     *
     * @var string
     * @see https://github.com/reddit-archive/reddit/wiki/OAuth2#getting-started
     */
    public $clientSecret = '';

    /**
     * The username of the Reddit account operating Snuze.
     *
     * @var string
     */
    public $username = '';

    /**
     * The password of the Reddit account operating Snuze.
     *
     * @var string
     */
    public $password = '';

    /**
     * The text to use in the User-Agent HTTP header. Reddit asks that users
     * provide a specially formatted string which identifies both you and your
     * application.
     *
     * @var string
     * @see https://github.com/reddit-archive/reddit/wiki/API#rules
     */
    public $userAgent = '';

    /**
     * An AccessToken object to hold authentication information.
     *
     * @var \snuze\Reddit\AccessToken
     */
    public $token = null;

    /**
     * A RateLimitBucket object to hold rate limit information.
     *
     * @var \snuze\RateLimitBucket
     */
    public $bucket = null;

    /**
     * Constructor. All parameters are mandatory.
     *
     * @param string $clientId The unique Reddit client ID of the script/bot
     * @param string $clientSecret The Reddit client secret for the script/bot
     * @param string $username The Reddit username to use
     * @param string $password The Reddit password to use
     * @param string $userAgent The User-Agent string to use
     * @param \snuze\Reddit\AccessToken $token A newly instantiated
     *      AccessToken object
     * @param \snuze\RateLimitBucket $bucket A newly instantiated
     *      RateLimitBucket object
     */
    public function __construct(string $clientId, string $clientSecret,
            string $username, string $password, string $userAgent,
            \snuze\Reddit\AccessToken $token, \snuze\RateLimitBucket $bucket) {

        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Set local properties */
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;
        $this->userAgent = $userAgent;
        $this->token = $token;
        $this->bucket = $bucket;
    }

}
