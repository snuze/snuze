<?php

namespace snuze\Reddit\Thing\Account;

use snuze\{
    Reddit\Thing\Thing
};

/**
 * The UserAccount class represents the data exposed about another user's
 * Reddit account. This is a (currently very small) superset of the common
 * properties in the parent Account class.
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
class UserAccount extends Account
{

    /**
     * Whether or not this account is a "friend" of the authenticated user
     * currently running Snuze
     *
     * @var bool
     */
    protected $isFriend = false;

    /**
     * Constructor.
     */
    public function __construct() {
        /* All Thing children must call parent ctor and set property names */
        parent::__construct(Thing::KIND_ACCOUNT);
        $this->_propertyNames = array_keys(get_object_vars($this));
        $this->debug('ctor args: ' . var_export(func_get_args(), true));
    }

    /**
     * Get whether or not this account is a "friend" of the authenticated user
     * currently running Snuze
     *
     * @return bool
     */
    public function getIsFriend(): bool {
        return $this->isFriend;
    }

    /**
     * Set whether or not this account is a "friend" of the authenticated user
     * currently running Snuze
     *
     * @param bool $isFriend
     * @return $this This object
     */
    protected function setIsFriend(bool $isFriend) {
        $this->isFriend = $isFriend;
        return $this;
    }

}
