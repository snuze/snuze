<?php

declare(strict_types=1);

namespace snuze\Request;

/**
 * AccessToken defines the structure and parameters of a request to the
 * /api/v1/access_token API endpoint.
 *
 * This endpoint returns an OAuth bearer token upon successful authentication.
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
class AccessToken extends Request
{

    /**
     * Defines the "grant_type" parameter name as valid. At present, only the
     * 'password' grant type is supported by Snuze.
     */
    const PARAM_GRANT_TYPE = 'grant_type';

    /**
     * Defines the "username" parameter name as valid
     */
    const PARAM_USERNAME = 'username';

    /**
     * Defines the "password" parameter name as valid
     */
    const PARAM_PASSWORD = 'password';

    /**
     * Constructor. Calls parent, then sets properties specific to this Request.
     */
    public function __construct() {

        /* All Request children must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Set HTTP verb */
        $this->setVerb(self::VERB_POST);

        /* Set endpoint path */
        $this->setEndpointPath('/api/v1/access_token');

        /* This request type doesn't require authentication */
        $this->setRequiresAuthentication(false);

        /* Override default API URI */
        $this->setApiUri(self::API_URIS['unauthenticated']);

        /* Set mandatory parameters for this Request type */
        $this->mandatoryParameters = [
            self::PARAM_GRANT_TYPE,
            self::PARAM_USERNAME,
            self::PARAM_PASSWORD,
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

        /*
         * Check the value of the grant_type field. At present, only the
         * 'password' grant type is supported by Snuze.
         */
        if ($this->getParameter(self::PARAM_GRANT_TYPE) !== 'password') {
            throw new \snuze\Exception\ArgumentException($this,
                    "Unsupported value '{$this->getParameter(self::PARAM_GRANT_TYPE)} ' "
                    . 'for parameter: ' . self::PARAM_GRANT_TYPE);
        }

        return true;
    }

}
