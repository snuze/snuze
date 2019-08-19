<?php

declare(strict_types=1);

namespace snuze\Reddit;

/**
 * An AccessToken represents the authentication information needed to interact
 * with many Reddit API endpoints.
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
class AccessToken extends \snuze\SnuzeObject implements \snuze\Interfaces\Jsonable
{

    /**
     * The Reddit account username that this access token was issued for
     *
     * @var string
     */
    private $username = null;

    /**
     * The access token assigned by Reddit's API
     *
     * @var string
     */
    private $token = null;

    /**
     * The Unix epoch corresponding to the time this access token expires
     *
     * @var int
     */
    private $expires = 0;

    /**
     * The family or families of actions to which this token grants its bearer
     * access.
     *
     * Currently, this will always be the asterisk '*' indicating the global
     * scope: the authenticated account can perform any supported API action
     * that it would be able to perform through Reddit's website.
     *
     * Future versions of Snuze may support individual scopes.
     *
     * @var string
     */
    private $scope = null;

    /**
     * The type of access token. Currently, this will always be 'bearer'.
     * Future versions of Snuze may support other types of tokens.
     *
     * @var string
     */
    private $tokenType = null;

    /**
     * Constructor.
     *
     * @param string $username The Reddit username for this access token
     * @throws \snuze\Exception\ArgumentException
     */
    public function __construct(string $username = null) {

        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Set local properties */
        $this->username = $username;
    }

    /**
     * Get the unix epoch timestamp at which this token expires
     *
     * @return int
     */
    public function getExpires(): int {
        return $this->expires;
    }

    /**
     * Get the scope of this token e.g. '*' or 'read'
     *
     * @return string
     */
    public function getScope(): string {
        return $this->scope;
    }

    /**
     * Return the raw access token string, if one has been set.
     *
     * @return string|null The access token, or null if there isn't one
     */
    public function getToken(): ?string {
        return $this->token;
    }

    /**
     * Get the type of token e.g. 'bearer'
     *
     * @return string
     */
    public function getTokenType(): string {
        return $this->tokenType;
    }

    /**
     * Get the Reddit account username this token was assigned to
     *
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Return whether or not this AccessToken object contains an unexpired token.
     *
     * @return bool True if a token exists and isn't expired; otherwise, false
     */
    public function isValid(): bool {
        return (!empty($this->token)) && $this->expires > time() + 60;
    }

    /**
     * Given the raw JSON response to an API request for an access token,
     * populate and return this AccessToken object.
     *
     * @param string $json The raw JSON response from Reddit's API server
     * @return \snuze\Reddit\AccessToken
     * @throws \snuze\Exception\RuntimeException
     */
    public function fromJson(string $json): AccessToken {

        $j = json_decode($json, true);

        /* Ensure all expected fields are present */
        foreach (['access_token', 'expires_in', 'scope', 'token_type'] as $key) {
            if (empty($j[$key])) {
                throw new \snuze\Exception\RuntimeException($this,
                        "Missing expected array element: {$key}");
            }
        }

        /* Set local parameters based on the JSON data */
        $this->token = $j['access_token'];
        $this->expires = time() + $j['expires_in'];
        $this->scope = $j['scope'];
        $this->tokenType = $j['token_type'];

        return $this;
    }

    /**
     * Returns a JSON-formatted string representing this token's properties.
     * Ideally, this should match Reddit's own data structure for returning
     * access tokens from the API. (Reddit doesn't return the username.)
     *
     * @return string
     */
    public function toJson(): string {
        return json_encode(
                [
                    'access_token' => $this->token,
                    'token_type'   => $this->tokenType,
                    'expires_in'   => $this->expires - time(),
                    'scope'        => $this->scope,
                ]
                , JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

}
