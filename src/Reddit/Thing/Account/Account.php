<?php

declare(strict_types=1);

namespace snuze\Reddit\Thing\Account;

/**
 * The Account class represents the common properties exposed for any Reddit
 * user account. You can't instantiate an Account; instead, you should create
 * a MyAccount or a UserAccount, depending on the API request type.
 *
 * Implementation warning: The $subreddit array is currently populated as-is,
 * without any further processing. Calling getSubreddit() will return an array
 * of key/value pairs defining the user's /u/username profile subreddit, or null
 * if one doesn't exist. This is subject to change in a future major version,
 * such that a Subreddit object may be returned instead.
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
abstract class Account extends \snuze\Reddit\Thing\Thing
{

    /**
     * A regular expression used to test whether or not an account username is
     * valid. Usernames must be between 3 and 20 characters long and may contain
     * only alphanumerics and underscores.
     */
    const REGEX_VALID_NAME = '|^[a-z0-9_]{3,20}$|i';

    /**
     * The account's internal Reddit identifier, e.g. "bva6"
     *
     * @var string
     */
    protected $id = null;

    /**
     * The account's comment karma score
     *
     * @var int
     */
    protected $commentKarma = 0;

    /**
     * Whether or not this account has ever had Reddit Gold
     *
     * @var bool
     */
    protected $hasSubscribed = true;

    /**
     * Whether or not this account has a verified email address
     *
     * @var bool
     */
    protected $hasVerifiedEmail = false;

    /**
     * Whether or not this account has enabled the "don't allow search
     * engines to index my user profile" preference
     *
     * @var bool
     */
    protected $hideFromRobots = false;

    /**
     * The full URI to this account's avatar image
     *
     * @var string
     */
    protected $iconImg = '';

    /**
     * Whether or not this account belongs to a Reddit administrator
     *
     * @var bool
     */
    protected $isEmployee = false;

    /**
     * Whether or not this account has Reddit Gold
     *
     * @var bool
     */
    protected $isGold = false;

    /**
     * Whether or not this account is a moderator of any subreddits
     *
     * @var bool
     */
    protected $isMod = false;

    /**
     * This account's link karma score
     *
     * @var int
     */
    protected $linkKarma = 1;

    /**
     * This account's username
     *
     * @var string
     */
    protected $name = '';

    /**
     * Whether or not this account has enabled the "make my snoovatar public"
     * preference
     *
     * @var bool
     */
    protected $prefShowSnoovatar = false;

    /**
     * An array of key/value pairs defining this account's /u/username
     * profile subreddit if one exists, or null otherwise
     * @var array|null
     * @todo perhaps return a Subreddit object in a future major version?
     */
    protected $subreddit = null;

    /**
     * Whether or not this account has been verified by Reddit admins as
     * actually belonging to the person or organization it claims to be
     *
     * @var bool
     */
    protected $verified = false;

    /**
     * Get this account's comment karma score
     *
     * @return int
     */
    public function getCommentKarma(): int {
        return $this->commentKarma;
    }

    /**
     * Set this account's comment karma score
     *
     * @param int $commentKarma
     * @return $this
     */
    protected function setCommentKarma(int $commentKarma) {
        $this->commentKarma = $commentKarma;
        return $this;
    }

    /**
     * Get whether or not this account has ever had Reddit Gold
     *
     * @return bool
     */
    public function getHasSubscribed(): bool {
        return $this->hasSubscribed;
    }

    /**
     * Set whether or not this account has ever had Reddit Gold
     *
     * @param bool $hasSubscribed
     * @return $this
     */
    protected function setHasSubscribed(bool $hasSubscribed) {
        $this->hasSubscribed = $hasSubscribed;
        return $this;
    }

    /**
     * Get whether or not this account has a verified email address
     *
     * @return bool
     */
    public function getHasVerifiedEmail(): bool {
        return $this->hasVerifiedEmail;
    }

    /**
     * Set whether or not this account has a verified email address
     *
     * @param bool $hasVerifiedEmail
     * @return $this
     */
    protected function setHasVerifiedEmail(bool $hasVerifiedEmail) {
        $this->hasVerifiedEmail = $hasVerifiedEmail;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "don't allow search
     * engines to index my user profile" preference
     *
     * @return bool
     */
    public function getHideFromRobots(): bool {
        return $this->hideFromRobots;
    }

    /**
     * Set whether or not this account has enabled the "don't allow search
     * engines to index my user profile" preference
     *
     * @param bool $hideFromRobots
     * @return $this
     */
    protected function setHideFromRobots(bool $hideFromRobots) {
        $this->hideFromRobots = $hideFromRobots;
        return $this;
    }

    /**
     * Get the full URI to this account's avatar image
     *
     * @return string
     */
    public function getIconImg(): string {
        return $this->iconImg;
    }

    /**
     * Set the full URI to this account's avatar image
     *
     * @param string $iconImg
     * @return $this
     */
    protected function setIconImg(string $iconImg) {
        $this->iconImg = $iconImg;
        return $this;
    }

    /**
     * Get whether or not this account belongs to a Reddit administrator
     *
     * @return bool
     */
    public function getIsEmployee(): bool {
        return $this->isEmployee;
    }

    /**
     * Set whether or not this account belongs to a Reddit administrator
     *
     * @param bool $isEmployee
     * @return $this
     */
    protected function setIsEmployee(bool $isEmployee) {
        $this->isEmployee = $isEmployee;
        return $this;
    }

    /**
     * Get whether or not this account has Reddit Gold
     *
     * @return bool
     */
    public function getIsGold(): bool {
        return $this->isGold;
    }

    /**
     * Set whether or not this account has Reddit Gold
     *
     * @param bool $isGold
     * @return $this
     */
    protected function setIsGold(bool $isGold) {
        $this->isGold = $isGold;
        return $this;
    }

    /**
     * Get whether or not this account is a moderator of any subreddits
     *
     * @return bool
     */
    public function getIsMod(): bool {
        return $this->isMod;
    }

    /**
     * Set whether or not this account is a moderator of any subreddits
     *
     * @param bool $isMod
     * @return $this
     */
    protected function setIsMod(bool $isMod) {
        $this->isMod = $isMod;
        return $this;
    }

    /**
     * Get this account's link karma score
     *
     * @return int
     */
    public function getLinkKarma(): int {
        return $this->linkKarma;
    }

    /**
     * Set this account's link karma score
     *
     * @param int $linkKarma
     * @return $this
     */
    protected function setLinkKarma(int $linkKarma) {
        $this->linkKarma = $linkKarma;
        return $this;
    }

    /**
     * Get this account's username
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set this account's username
     *
     * @param string $name
     * @return $this
     * @throws \snuze\Exception\ArgumentException
     */
    protected function setName(string $name) {
        /* Test username for validity */
        if (!preg_match(self::REGEX_VALID_NAME, $name)) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid username');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "make my snoovatar public"
     * preference
     *
     * @return bool
     */
    public function getPrefShowSnoovatar(): bool {
        return $this->prefShowSnoovatar;
    }

    /**
     * Set whether or not this account has enabled the "make my snoovatar public"
     * preference
     *
     * @param bool $prefShowSnoovatar
     * @return $this
     */
    protected function setPrefShowSnoovatar(bool $prefShowSnoovatar) {
        $this->prefShowSnoovatar = $prefShowSnoovatar;
        return $this;
    }

    /**
     * Get an array of key/value pairs defining this account's /u/username
     * profile subreddit if one exists, or null otherwise
     *
     * @return array|null
     */
    public function getSubreddit(): ?array {
        return $this->subreddit;
    }

    /**
     * Set an array of key/value pairs defining this account's /u/username
     * profile subreddit if one exists, or null otherwise
     *
     * @param array $subreddit
     * @return $this
     */
    protected function setSubreddit(array $subreddit = null) {
        $this->subreddit = $subreddit;
        return $this;
    }

    /**
     * Get whether or not this account has been verified by Reddit admins as
     * actually belonging to the person or organization it claims to be
     *
     * @return bool
     */
    public function getVerified(): bool {
        return $this->verified;
    }

    /**
     * Set whether or not this account has been verified by Reddit admins as
     * actually belonging to the person or organization it claims to be
     *
     * @param bool $verified
     * @return $this
     */
    protected function setVerified(bool $verified) {
        $this->verified = $verified;
        return $this;
    }

}
