<?php

namespace snuze\Persistence\Interfaces;

/**
 * The SubredditMapperInterface is part of the storage provider design.
 *
 * It defines any additional methods that must be implemented for persisting
 * a Subreddit object, above and beyond the requirements in MapperInterface.
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
interface SubredditMapperInterface extends MapperInterface
{

    /**
     * Implementers must expose a retrieve() method that accepts a subreddit's
     * display name (e.g. 'funny') and returns the Subreddit object, or null if
     * the subreddit isn't persisted.
     *
     * @param string $displayName The display name of the subreddit to retrieve
     * @return \snuze\Reddit\Thing\Subreddit|null
     */
    public function retrieve($displayName);

    /**
     * Implementers must expose a method to return a Subreddit by its internal
     * Reddit "thing" ID, *without* "t5_" prepended, e.g. "2tlk9".
     *
     * @param string $id The subreddit's Reddit ID *without* "t5_" prepended
     * @return \snuze\Reddit\Thing\Subreddit|null
     */
    public function retrieveById(string $id): ?\snuze\Reddit\Thing\Subreddit;
}
