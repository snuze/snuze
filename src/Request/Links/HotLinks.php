<?php

declare(strict_types=1);

namespace snuze\Request\Links;

/**
 * HotLinks defines the structure and parameters of a request to the
 * /r/[subreddit]/hot API endpoint.
 *
 * This endpoint returns links from the specified subreddit, presented in the
 * "hot" sort order.
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
class HotLinks extends Links
{

    /**
     * Defines the "g" parameter as valid
     *
     * A geographic region code, as specified by Reddit, that can be used as a
     * hint when requesting the "hot" listing for a subreddit. This is ONLY a
     * valid parameter when the sort order is SORT_HOT, but is never required.
     * The value is restricted to a predefined list of options set by Reddit
     * (see ::validate(), below).
     */
    const PARAM_GEO = 'g';

    /**
     * Define an array of acceptable values for PARAM_GEO.
     *
     * @see https://www.reddit.com/dev/api/#GET_hot
     */
    const GEO_VALUES = [
        'GLOBAL', 'AR', 'AU', 'BG', 'CA', 'CL', 'CO', 'CZ', 'FI', 'GB', 'GR',
        'HR', 'HU', 'IE', 'IN', 'IS', 'JP', 'MX', 'MY', 'NZ', 'PH', 'PL',
        'PR', 'PT', 'RO', 'RS', 'SE', 'SG', 'TH', 'TR', 'TW', 'US', 'US_AK',
        'US_AL', 'US_AR', 'US_AZ', 'US_CA', 'US_CO', 'US_CT', 'US_DC', 'US_DE',
        'US_FL', 'US_GA', 'US_HI', 'US_IA', 'US_ID', 'US_IL', 'US_IN', 'US_KS',
        'US_KY', 'US_LA', 'US_MA', 'US_MD', 'US_ME', 'US_MI', 'US_MN', 'US_MO',
        'US_MS', 'US_MT', 'US_NC', 'US_ND', 'US_NE', 'US_NH', 'US_NJ', 'US_NM',
        'US_NV', 'US_NY', 'US_OH', 'US_OK', 'US_OR', 'US_PA', 'US_RI', 'US_SC',
        'US_SD', 'US_TN', 'US_TX', 'US_UT', 'US_VA', 'US_VT', 'US_WA', 'US_WI',
        'US_WV', 'US_WY'
    ];

    public function __construct(string $subredditName) {

        /* All Links children must call parent ctor */
        parent::__construct($subredditName, Links::SORT_HOT);
        $this->debug('ctor args: ' . var_export(func_get_args(), true));
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
         * If PARAM_GEO is set, check that its value is permissible.
         */
        if (!empty($this->getParameter(self::PARAM_GEO))) {
            if (!in_array($this->getParameter(self::PARAM_GEO), self::GEO_VALUES)) {
                throw new \snuze\Exception\ArgumentException($this,
                        "Unsupported value '{$this->getParameter(self::PARAM_GEO)}' "
                        . 'for parameter: ' . self::PARAM_GEO);
            }
        }

        return true;
    }

}
