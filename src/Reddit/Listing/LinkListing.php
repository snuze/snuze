<?php

namespace snuze\Reddit\Listing;

/**
 * A LinkListing is a more strict implementation of Listing, which only
 * accepts Link objects as its children.
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
class LinkListing extends Listing
{

    /**
     * Add a Link to the collection.
     *
     * Overrides parent to enforce that only Link objects may be added.
     *
     * @param \snuze\Reddit\Thing\Link $link A Link object to add to
     *      the collection
     * @return void
     * @throws \snuze\Exception\ArgumentException
     */
    public function add($link): void {

        /* Test the argument */
        if (!$link instanceof \snuze\Reddit\Thing\Link) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Argument to add() must be a Link object');
        }

        $this->children[] = $link;
    }

}
