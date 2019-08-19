<?php

namespace snuze\Reddit\Listing;

/**
 * An AccountListing is a more strict implementation of Listing, which only
 * accepts Account subtype objects (UserAccount or MyAccount) as its children.
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
class AccountListing extends Listing
{

    /**
     * Add an Account to the collection.
     *
     * Overrides parent to enforce that only Account objects may be added.
     *
     * @param \snuze\Reddit\Thing\Account\Account $account An Account
     *      descendant object to add to the collection
     * @return void
     * @throws \snuze\Exception\ArgumentException
     */
    public function add($account): void {

        /* Test the argument */
        if (!$account instanceof \snuze\Reddit\Thing\Account\Account) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Argument to add() must be an Account subtype object');
        }

        $this->children[] = $account;
    }

}
