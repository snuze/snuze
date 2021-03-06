<?php

namespace snuze\Persistence\Interfaces;

/**
 * The AccessTokenMapperInterface is part of the storage provider design.
 * 
 * It defines additional methods that must be implemented for persisting
 * an AccessToken object, above and beyond the requirements in MapperInterface.
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
interface AccessTokenMapperInterface extends MapperInterface
{

    /**
     * Implementers must expose a method to return the newest unexpired and
     * valid AccessToken for a given user, if one exists.
     *
     * @param string $username The username for whom to look for an AccessToken
     * @return \snuze\Reddit\AccessToken|null
     */
    public function retrieveOneFor(string $username): ?\snuze\Reddit\AccessToken;

    /**
     * Implementers must expose a method to delete stale AccessToken objects
     * from storage.
     *
     * @param int $olderThan A number of seconds. The method should delete any
     *      AccessTokens that expired more than this many seconds ago.
     */
    public function purge(int $olderThan = 86400);
}
