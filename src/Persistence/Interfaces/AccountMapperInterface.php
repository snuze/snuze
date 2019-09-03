<?php

namespace snuze\Persistence\Interfaces;

/**
 * The AccountMapperInterface is part of the storage provider design.
 *
 * It defines any additional methods that must be implemented for persisting
 * an Account subtype object, above and beyond the requirements in MapperInterface.
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
interface AccountMapperInterface extends MapperInterface
{

    /**
     * Implementers must expose a retrieve() method that accepts an account's
     * username and returns a UserAccount object, or null if the account isn't
     * persisted.
     *
     * @param string $username The username of the account to retrieve
     * @return \snuze\Reddit\Thing\Account\UserAccount|null
     */
    public function retrieve($username);

    /**
     * Implementers must expose a method to return an account by its internal
     * Reddit "thing" ID, *without* "t2_" prepended, e.g. "bva2".
     *
     * @param string $id The account's Reddit ID *without* "t2_" prepended
     * @return \snuze\Reddit\Thing\Account\UserAccount|null
     */
    public function retrieveById(string $id): ?\snuze\Reddit\Thing\Account\UserAccount;
}
