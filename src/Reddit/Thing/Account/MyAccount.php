<?php

declare(strict_types=1);

namespace snuze\Reddit\Thing\Account;

use snuze\Reddit\Thing\Thing;

/**
 * The MyAccount class represents the data exposed about the authenticated
 * user's own account. This is a significant superset of the common properties
 * in the parent Account class.
 *
 * An attempt has been made to map all fields supplied by the API, regardless
 * of their utility (or lack thereof).
 *
 * Implementation warning: The $subreddit array is currently populated as-is,
 * without any further processing. Calling getSubreddit() will return an array
 * of key/value pairs defining the user's /u/username profile subreddit, or null
 * if one doesn't exist. This is subject to change in a future major version,
 * such that a Subreddit object may be returned instead.
 *
 * Implementation warning: The $features array is currently populated as-is,
 * without any further processing. Calling getFeatures() will return an array
 * of key/value pairs defining the user's settings for various Reddit features,
 * tests, and experiments. This is subject to change in a future major version,
 * such that some other value(s) may be returned instead.
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
class MyAccount extends Account
{
    /*
     * Properties specific to the authenticated user's own account. These are
     * documented alongside their accessors, below.
     */

    /**
     * Whether or not this account is allowed to create a subreddit
     *
     * @var bool
     */
    protected $canCreateSubreddit = false;

    /**
     * The number of Reddit Premium coins this account has available
     *
     * @var int
     */
    protected $coins = 0;

    /**
     * An array of key/value pairs defining the account's settings for
     * various Reddit features, tests, and experiments
     *
     * @var array
     */
    protected $features = [];

    /**
     * Whether or not this account is required to change its password
     * for security purposes
     *
     * @var bool
     */
    protected $forcePasswordReset = false;

    /**
     * The number of Reddit Gold creddits this account has available
     *
     * @var int
     */
    protected $goldCreddits = 0;

    /**
     * The epoch time at which this account's Reddit Gold/Premium expires,
     * or null if there's no subscription.
     *
     * @var int|null
     */
    protected $goldExpiration = null;

    /**
     * Whether or not this account is subscribed to Reddit Premium via the
     * Android application
     *
     * @var bool
     */
    protected $hasAndroidSubscription = false;

    /**
     * Whether or not this account has a third-party social media account
     * linked to it
     *
     * @var bool
     */
    protected $hasExternalAccount = false;

    /**
     * Whether or not this account has an active Reddit Gold subscription
     *
     * @var bool
     */
    protected $hasGoldSubscription = false;

    /**
     * Whether or not this account is subscribed to Reddit Premium via the
     * Apple iOS application
     *
     * @var bool
     */
    protected $hasIosSubscription = false;

    /**
     * Whether or not this account has unread direct messages
     *
     * @var bool
     */
    protected $hasMail = false;

    /**
     * Whether or not this account has unread modmail messages
     *
     * @var bool
     */
    protected $hasModMail = false;

    /**
     * Whether or not this account is subscribed to Reddit Premium via PayPal
     *
     * @var bool
     */
    protected $hasPaypalSubscription = false;

    /**
     * Whether or not this account is subscribed to Reddit Premium via Stripe
     *
     * @var bool
     */
    protected $hasStripeSubscription = false;

    /**
     * Whether or not this account has ever had new-style Reddit Premium
     *
     * @var bool
     */
    protected $hasSubscribedToPremium = false;

    /**
     * Whether or not this account has visited its redesign-style profile
     *
     * @var bool
     */
    protected $hasVisitedNewProfile = false;

    /**
     * The number of unread direct messages this account has
     *
     * @var int
     */
    protected $inboxCount = 0;

    /**
     * Whether or not this account has opted in to beta participation
     *
     * @var bool
     */
    protected $inBeta = false;

    /**
     * Whether or not this account is eligible to receive chat messages
     *
     * @var bool
     */
    protected $inChat = false;

    /**
     * Whether or not this account has opted in to beta test Digg 5.0
     *
     * @var bool
     */
    protected $inRedesignBeta = false;

    /**
     * Whether or not this account is a Reddit advertiser
     *
     * @var bool
     */
    protected $isSponsor = false;

    /**
     * Whether or not this account is suspended for rule violations
     *
     * @var bool
     */
    protected $isSuspended = false;

    /**
     * Whether or not this account is enrolled in the new modmail system
     *
     * @var bool|null
     * @todo make sure the docs are right here. Is this "you have the new
     *      modmail interface" or "you have unread modmail messages"?
     */
    protected $newModmailExists = null;

    /**
     * The number of other users this account has friended
     *
     * @var int
     */
    protected $numFriends = 0;

    /**
     * The OAuth client ID this account is currently authenticated with
     * (this will correspond to the currently-running script application)
     *
     * @var string
     */
    protected $oauthClientId = '';

    /**
     * Whether or not this account has enabled the "I am over eighteen years
     * old and willing to view adult content" preference
     *
     * @var bool
     */
    protected $over18 = false;

    /**
     * Whether or not this account has enabled the auto-play preference
     *
     * @var bool
     * @todo verify what exactly this corresponds to
     */
    protected $prefAutoplay = false;

    /**
     * Whether or not this account has enabled the "show me links I've
     * recently viewed" preference. Reddit supplies this one as an integer
     * instead of a boolean.
     *
     * @var int
     */
    protected $prefClickgadget = 0;

    /**
     * The locality code, if any, that Reddit is using as a hint to build a
     * geographically-oriented "/r/popular" list for this account
     *
     * @var string
     */
    protected $prefGeopopular = '';

    /**
     * Whether or not this account has enabled the night mode theme
     *
     * @var bool
     */
    protected $prefNightmode = false;

    /**
     * Whether or not this account has enabled the "hide images for NSFW/18+
     * content" preference
     *
     * @var bool
     */
    protected $prefNoProfanity = false;

    /**
     * Whether or not this account has enabled the "show trending subreddits
     * on the home feed" preference
     *
     * @var bool
     */
    protected $prefShowTrending = false;

    /**
     * Whether or not this account displays a linked Twitter account on
     * its profile
     *
     * @var bool
     */
    protected $prefShowTwitter = false;

    /**
     * Whether this account has some preference or another enabled
     *
     * @var bool
     * @todo verify what exactly this corresponds to
     */
    protected $prefTopKarmaSubreddits = true;

    /**
     * Whether or not this account has the "Autoplay Reddit videos on the
     * desktop comments page" preference enabled
     *
     * @var bool
     */
    protected $prefVideoAutoplay = false;

    /**
     * Whether or not this account has been shown a layout switch interface
     *
     * @var bool
     */
    protected $seenLayoutSwitch = false;

    /**
     * Whether or not this account has been shown a "buying Reddit Premium
     * lets you disable (some) ads" dialog
     *
     * @var bool
     */
    protected $seenPremiumAdblockModal = false;

    /**
     * Whether or not this account has been shown a dialog about the redesign
     *
     * @var bool
     */
    protected $seenRedesignModal = false;

    /**
     * Whether or not this account has been shown a first-time user interface
     * about the subreddit chat feature
     *
     * @var bool
     */
    protected $seenSubredditChatFtux = false;

    /**
     * If this account is suspended for rule violations and is eligible to
     * be reinstated, the unix epoch at which time that will occur, else null
     *
     * @var float|null
     */
    protected $suspensionExpirationUtc = null;

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
     * Get whether or not this account is allowed to create a subreddit
     *
     * @return bool
     */
    public function getCanCreateSubreddit(): bool {
        return $this->canCreateSubreddit;
    }

    /**
     * Set whether or not this account is allowed to create a subreddit
     *
     * @param bool $canCreateSubreddit
     * @return $this
     */
    protected function setCanCreateSubreddit(bool $canCreateSubreddit) {
        $this->canCreateSubreddit = $canCreateSubreddit;
        return $this;
    }

    /**
     * Get the number of Reddit Premium coins this account has available
     *
     * @return int
     */
    public function getCoins(): int {
        return $this->coins;
    }

    /**
     * Set the number of Reddit Premium coins this account has available
     *
     * @param int $coins
     * @return $this
     */
    protected function setCoins(int $coins) {
        $this->coins = $coins;
        return $this;
    }

    /**
     * Get the array of key/value pairs defining the account's settings for
     * various Reddit features, tests, and experiments
     *
     * @return array
     */
    public function getFeatures(): array {
        return $this->features;
    }

    /**
     * Set the array of key/value pairs defining the account's settings for
     * various Reddit features, tests, and experiments
     *
     * @param array $features
     * @return $this
     */
    protected function setFeatures(array $features) {
        $this->features = $features;
        return $this;
    }

    /**
     * Get whether or not this account is required to change its password
     * for security purposes
     *
     * @return bool
     */
    public function getForcePasswordReset(): bool {
        return $this->forcePasswordReset;
    }

    /**
     * Set whether or not this account is required to change its password
     * for security purposes
     *
     * @param bool $forcePasswordReset
     * @return $this
     */
    protected function setForcePasswordReset(bool $forcePasswordReset) {
        $this->forcePasswordReset = $forcePasswordReset;
        return $this;
    }

    /**
     * Get the number of Reddit Gold creddits this account has available
     *
     * @return int
     */
    public function getGoldCreddits(): int {
        return $this->goldCreddits;
    }

    /**
     * Set the number of Reddit Gold creddits this account has available
     *
     * @param int $goldCreddits
     * @return $this
     */
    protected function setGoldCreddits(int $goldCreddits) {
        $this->goldCreddits = $goldCreddits;
        return $this;
    }

    /**
     * Get the epoch time at which this account's Reddit Gold/Premium expires,
     * or null if there's no subscription.
     *
     * @return float|null A unix epoch timestamp, as a float; or null
     */
    public function getGoldExpiration(): ?float {
        return $this->goldExpiration;
    }

    /**
     * Set the epoch time at which this account's Reddit Gold/Premium expires,
     * or null if there's no subscription.
     *
     * @param float $goldExpiration A unix epoch timestamp, as a float; or null
     * @return $this
     */
    protected function setGoldExpiration(float $goldExpiration = null) {
        $this->goldExpiration = $goldExpiration;
        return $this;
    }

    /**
     * Get whether or not this account is subscribed to Reddit Premium via the
     * Android application
     *
     * @return bool
     */
    public function getHasAndroidSubscription(): bool {
        return $this->hasAndroidSubscription;
    }

    /**
     * Set whether or not this account is subscribed to Reddit Premium via the
     * Android application
     *
     * @param bool $hasAndroidSubscription
     * @return $this
     */
    protected function setHasAndroidSubscription(bool $hasAndroidSubscription) {
        $this->hasAndroidSubscription = $hasAndroidSubscription;
        return $this;
    }

    /**
     * Get whether or not this account has a third-party social media account
     * linked to it
     *
     * @return bool
     */
    public function getHasExternalAccount(): bool {
        return $this->hasExternalAccount;
    }

    /**
     * Set whether or not this account has a third-party social media account
     * linked to it
     *
     * @param bool $hasExternalAccount
     * @return $this
     */
    protected function setHasExternalAccount(bool $hasExternalAccount) {
        $this->hasExternalAccount = $hasExternalAccount;
        return $this;
    }

    /**
     * Get whether or not this account has an active Reddit Gold subscription
     *
     * @return bool
     */
    public function getHasGoldSubscription(): bool {
        return $this->hasGoldSubscription;
    }

    /**
     * Set whether or not this account has an active Reddit Gold subscription
     *
     * @param bool $hasGoldSubscription
     * @return $this
     */
    protected function setHasGoldSubscription(bool $hasGoldSubscription) {
        $this->hasGoldSubscription = $hasGoldSubscription;
        return $this;
    }

    /**
     * Get whether or not this account is subscribed to Reddit Premium via the
     * Apple iOS application
     *
     * @return bool
     */
    public function getHasIosSubscription(): bool {
        return $this->hasIosSubscription;
    }

    /**
     * Set whether or not this account is subscribed to Reddit Premium via the
     * Apple iOS application
     *
     * @param bool $hasIosSubscription
     * @return $this
     */
    protected function setHasIosSubscription(bool $hasIosSubscription) {
        $this->hasIosSubscription = $hasIosSubscription;
        return $this;
    }

    /**
     * Get whether or not this account has unread direct messages
     *
     * @return bool
     */
    public function getHasMail(): bool {
        return $this->hasMail;
    }

    /**
     * Set whether or not this account has unread direct messages
     *
     * @param bool $hasMail
     * @return $this
     */
    protected function setHasMail(bool $hasMail) {
        $this->hasMail = $hasMail;
        return $this;
    }

    /**
     * Get whether or not this account has unread modmail messages
     *
     * @return bool
     */
    public function getHasModMail(): bool {
        return $this->hasModMail;
    }

    /**
     * Set whether or not this account has unread modmail messages
     *
     * @param bool $hasModMail
     * @return $this
     */
    protected function setHasModMail(bool $hasModMail) {
        $this->hasModMail = $hasModMail;
        return $this;
    }

    /**
     * Get whether or not this account is subscribed to Reddit Premium via
     * PayPal
     *
     * @return bool
     */
    public function getHasPaypalSubscription(): bool {
        return $this->hasPaypalSubscription;
    }

    /**
     * Set whether or not this account is subscribed to Reddit Premium via
     * PayPal
     *
     * @param bool $hasPaypalSubscription
     * @return $this
     */
    protected function setHasPaypalSubscription(bool $hasPaypalSubscription) {
        $this->hasPaypalSubscription = $hasPaypalSubscription;
        return $this;
    }

    /**
     * Get whether or not this account is subscribed to Reddit Premium via
     * Stripe
     *
     * @return bool
     */
    public function getHasStripeSubscription(): bool {
        return $this->hasStripeSubscription;
    }

    /**
     * Set whether or not this account is subscribed to Reddit Premium via
     * Stripe
     *
     * @param bool $hasStripeSubscription
     * @return $this
     */
    protected function setHasStripeSubscription(bool $hasStripeSubscription) {
        $this->hasStripeSubscription = $hasStripeSubscription;
        return $this;
    }

    /**
     * Get whether or not this account has ever had new-style Reddit Premium
     *
     * @return bool
     */
    public function getHasSubscribedToPremium(): bool {
        return $this->hasSubscribedToPremium;
    }

    /**
     * Set whether or not this account has ever had new-style Reddit Premium
     *
     * @param bool $hasSubscribedToPremium
     * @return $this
     */
    protected function setHasSubscribedToPremium(bool $hasSubscribedToPremium) {
        $this->hasSubscribedToPremium = $hasSubscribedToPremium;
        return $this;
    }

    /**
     * Get whether or not this account has visited its redesign-style profile
     *
     * @return bool
     */
    public function getHasVisitedNewProfile(): bool {
        return $this->hasVisitedNewProfile;
    }

    /**
     * Set whether or not this account has visited its redesign style profile
     *
     * @param bool $hasVisitedNewProfile
     * @return $this
     */
    protected function setHasVisitedNewProfile(bool $hasVisitedNewProfile) {
        $this->hasVisitedNewProfile = $hasVisitedNewProfile;
        return $this;
    }

    /**
     * Get whether or not this account has opted in to beta participation
     *
     * @return bool
     */
    public function getInBeta(): bool {
        return $this->inBeta;
    }

    /**
     * Set whether or not this account has opted in to beta participation
     *
     * @param bool $inBeta
     * @return $this
     */
    protected function setInBeta(bool $inBeta) {
        $this->inBeta = $inBeta;
        return $this;
    }

    /**
     * Get the number of unread direct messages this account has
     *
     * @return int
     */
    public function getInboxCount(): int {
        return $this->inboxCount;
    }

    /**
     * Set the number of unread direct messages this account has
     *
     * @param int $inboxCount
     * @return $this
     */
    protected function setInboxCount(int $inboxCount) {
        $this->inboxCount = $inboxCount;
        return $this;
    }

    /**
     * Get whether or not this account is eligible to receive chat messages
     *
     * @return bool
     */
    public function getInChat(): bool {
        return $this->inChat;
    }

    /**
     * Set whether or not this account is eligible to receive chat messages
     *
     * @param bool $inChat
     * @return $this
     */
    protected function setInChat(bool $inChat) {
        $this->inChat = $inChat;
        return $this;
    }

    /**
     * Get whether or not this account has opted in to beta test Digg 5.0
     *
     * @return bool
     */
    public function getInRedesignBeta(): bool {
        return $this->inRedesignBeta;
    }

    /**
     * Set whether or not this account has opted in to beta test Digg 5.0
     *
     * @param bool $inRedesignBeta
     * @return $this
     */
    protected function setInRedesignBeta(bool $inRedesignBeta) {
        $this->inRedesignBeta = $inRedesignBeta;
        return $this;
    }

    /**
     * Get whether or not this account is a Reddit advertiser
     *
     * @return bool
     */
    public function getIsSponsor(): bool {
        return $this->isSponsor;
    }

    /**
     * Set whether or not this account is a Reddit advertiser
     *
     * @param bool $isSponsor
     * @return $this
     */
    protected function setIsSponsor(bool $isSponsor) {
        $this->isSponsor = $isSponsor;
        return $this;
    }

    /**
     * Get whether or not this account is suspended for rule violations
     *
     * @return bool
     */
    public function getIsSuspended(): bool {
        return $this->isSuspended;
    }

    /**
     * Set whether or not this account is suspended for rule violations
     *
     * @param bool $isSuspended
     * @return $this
     */
    protected function setIsSuspended(bool $isSuspended) {
        $this->isSuspended = $isSuspended;
        return $this;
    }

    /**
     * Get whether or not this account is enrolled in the new modmail system
     *
     * @return bool|null
     * @todo make sure the docs are right here. Is this "you have the new
     *      modmail" or "you have unread modmail messages"?
     */
    public function getNewModmailExists(): ?bool {
        return $this->newModmailExists;
    }

    /**
     * Set whether or not this account is enrolled in the new modmail system
     *
     * @param bool $newModmailExists
     * @return $this
     * @todo make sure the docs are right here. Is this "you have the new
     *      modmail interface" or "you have unread modmail messages"?
     */
    protected function setNewModmailExists(bool $newModmailExists = null) {
        $this->newModmailExists = $newModmailExists;
        return $this;
    }

    /**
     * Get the number of other users this account has friended
     *
     * @return int
     */
    public function getNumFriends(): int {
        return $this->numFriends;
    }

    /**
     * Set the number of other users this account has friended
     *
     * @param int $numFriends
     * @return $this
     */
    protected function setNumFriends(int $numFriends) {
        $this->numFriends = $numFriends;
        return $this;
    }

    /**
     * Get the OAuth client ID this account is currently authenticated with
     * (this will correspond to the currently-running script application)
     *
     * @return string
     */
    public function getOauthClientId(): string {
        return $this->oauthClientId;
    }

    /**
     * Set the OAuth client ID this account is currently authenticated with
     *
     * @param string $oauthClientId
     * @return $this
     */
    protected function setOauthClientId(string $oauthClientId) {
        $this->oauthClientId = $oauthClientId;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "I am over eighteen years
     * old and willing to view adult content" preference
     *
     * @return bool
     */
    public function getOver18(): bool {
        return $this->over18;
    }

    /**
     * Set whether or not this account has enabled the "I am over eighteen years
     * old and willing to view adult content" preference
     *
     * @param bool $over18
     * @return $this
     */
    protected function setOver18(bool $over18) {
        $this->over18 = $over18;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the auto-play preference
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getPrefAutoplay(): bool {
        return $this->prefAutoplay;
    }

    /**
     * Set whether or not this account has enabled the auto-play preference
     *
     * @param bool $prefAutoplay
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setPrefAutoplay(bool $prefAutoplay) {
        $this->prefAutoplay = $prefAutoplay;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "show me links I've
     * recently viewed" preference. Reddit supplies this one as an integer
     * instead of a boolean.
     *
     * @return int
     */
    public function getPrefClickgadget(): int {
        return $this->prefClickgadget;
    }

    /**
     * Set whether or not this account has enabled the "show me links I've
     * recently viewed" preference. Reddit supplies this one as an integer
     * instead of a boolean.
     *
     * @param int $prefClickgadget
     * @return $this
     */
    protected function setPrefClickgadget(int $prefClickgadget) {
        $this->prefClickgadget = $prefClickgadget;
        return $this;
    }

    /**
     * Get the locality code, if any, that Reddit is using as a hint to build a
     * geographically-oriented "/r/popular" list for this account
     *
     * @return string
     */
    public function getPrefGeopopular(): string {
        return $this->prefGeopopular;
    }

    /**
     * Set the locality code, if any, that Reddit is using as a hint to build a
     * geographically-oriented "/r/popular" list for this account
     *
     * @param string $prefGeopopular
     * @return $this
     */
    protected function setPrefGeopopular(string $prefGeopopular) {
        $this->prefGeopopular = $prefGeopopular;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the night mode theme
     *
     * @return bool
     */
    public function getPrefNightmode(): bool {
        return $this->prefNightmode;
    }

    /**
     * Set whether or not this account has enabled the night mode theme
     *
     * @param bool $prefNightmode
     * @return $this
     */
    protected function setPrefNightmode(bool $prefNightmode) {
        $this->prefNightmode = $prefNightmode;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "hide images for NSFW/18+
     * content" preference
     *
     * @return bool
     */
    public function getPrefNoProfanity(): bool {
        return $this->prefNoProfanity;
    }

    /**
     * Set whether or not this account has enabled the "hide images for NSFW/18+
     * content" preference
     *
     * @param bool $prefNoProfanity
     * @return $this
     */
    protected function setPrefNoProfanity(bool $prefNoProfanity) {
        $this->prefNoProfanity = $prefNoProfanity;
        return $this;
    }

    /**
     * Get whether or not this account has enabled the "show trending subreddits
     * on the home feed" preference
     *
     * @return bool
     */
    public function getPrefShowTrending(): bool {
        return $this->prefShowTrending;
    }

    /**
     * Set whether or not this account has enabled the "show trending subreddits
     * on the home feed" preference
     *
     * @param bool $prefShowTrending
     * @return $this
     */
    protected function setPrefShowTrending(bool $prefShowTrending) {
        $this->prefShowTrending = $prefShowTrending;
        return $this;
    }

    /**
     * Get whether or not this account displays a linked Twitter account on
     * its profile
     *
     * @return bool
     * @see MyAccount::getHasExternalAccount()
     */
    public function getPrefShowTwitter(): bool {
        return $this->prefShowTwitter;
    }

    /**
     * Set whether or not this account displays a linked Twitter account on
     * its profile
     *
     * @param bool $prefShowTwitter
     * @return $this
     * @see MyAccount::setHasExternalAccount()
     */
    protected function setPrefShowTwitter(bool $prefShowTwitter) {
        $this->prefShowTwitter = $prefShowTwitter;
        return $this;
    }

    /**
     * Get whether this account has some preference or another enabled
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getPrefTopKarmaSubreddits(): bool {
        return $this->prefTopKarmaSubreddits;
    }

    /**
     * Set whether this account has some preference or another enabled
     *
     * @param bool $prefTopKarmaSubreddits
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setPrefTopKarmaSubreddits(bool $prefTopKarmaSubreddits) {
        $this->prefTopKarmaSubreddits = $prefTopKarmaSubreddits;
        return $this;
    }

    /**
     * Get whether or not this account has the "Autoplay Reddit videos on the
     * desktop comments page" preference enabled
     *
     * @return bool
     */
    public function getPrefVideoAutoplay(): bool {
        return $this->prefVideoAutoplay;
    }

    /**
     * Set whether or not this account has the "Autoplay Reddit videos on the
     * desktop comments page" preference enabled
     *
     * @param bool $prefVideoAutoplay
     * @return $this
     */
    protected function setPrefVideoAutoplay(bool $prefVideoAutoplay) {
        $this->prefVideoAutoplay = $prefVideoAutoplay;
        return $this;
    }

    /**
     * Get whether or not this account has been shown a layout switch interface
     *
     * @return bool
     */
    public function getSeenLayoutSwitch(): bool {
        return $this->seenLayoutSwitch;
    }

    /**
     * Set whether or not this account has been shown a layout switch interface
     *
     * @param bool $seenLayoutSwitch
     * @return $this
     */
    protected function setSeenLayoutSwitch(bool $seenLayoutSwitch) {
        $this->seenLayoutSwitch = $seenLayoutSwitch;
        return $this;
    }

    /**
     * Get whether or not this account has been shown a "buying Reddit Premium
     * lets you disable (some) ads" dialog
     *
     * @return bool
     */
    public function getSeenPremiumAdblockModal(): bool {
        return $this->seenPremiumAdblockModal;
    }

    /**
     * Set whether or not this account has been shown a "buying Reddit Premium
     * lets you disable (some) ads" dialog
     *
     * @param bool $seenPremiumAdblockModal
     * @return $this
     */
    protected function setSeenPremiumAdblockModal(bool $seenPremiumAdblockModal) {
        $this->seenPremiumAdblockModal = $seenPremiumAdblockModal;
        return $this;
    }

    /**
     * Get whether or not this account has been shown a dialog about the redesign
     *
     * @return bool
     */
    public function getSeenRedesignModal(): bool {
        return $this->seenRedesignModal;
    }

    /**
     * Set whether or not this account has been shown a dialog about the redesign
     *
     * @param bool $seenRedesignModal
     * @return $this
     */
    protected function setSeenRedesignModal(bool $seenRedesignModal) {
        $this->seenRedesignModal = $seenRedesignModal;
        return $this;
    }

    /**
     * Get whether or not this account has been shown a first-time user
     * interface about the subreddit chat feature
     *
     * @return bool
     */
    public function getSeenSubredditChatFtux(): bool {
        return $this->seenSubredditChatFtux;
    }

    /**
     * Set whether or not this account has been shown a first-time user
     * interface about the subreddit chat feature
     *
     * @param bool $seenSubredditChatFtux
     * @return $this
     */
    protected function setSeenSubredditChatFtux(bool $seenSubredditChatFtux) {
        $this->seenSubredditChatFtux = $seenSubredditChatFtux;
        return $this;
    }

    /**
     * If this account is suspended for rule violations and is eligible to
     * be reinstated, get the unix epoch at which time that will occur
     *
     * @return float|null A unix epoch timestamp, as a float; or null
     */
    public function getSuspensionExpirationUtc(): ?float {
        return $this->suspensionExpirationUtc;
    }

    /**
     * If this account is suspended for rule violations and is eligible to
     * be reinstated, set the unix epoch at which time that will occur
     *
     * @param float $suspensionExpirationUtc A unix epoch timestamp, as a float; or null
     * @return $this
     */
    protected function setSuspensionExpirationUtc(float $suspensionExpirationUtc
            = null) {
        $this->suspensionExpirationUtc = $suspensionExpirationUtc;
        return $this;
    }

    /**
     * Overrides parent to perform specialized data handling. Reddit doesn't
     * return the typical "thing" data structure in response to this type of
     * request. Here, the response is massaged into the expected format so it
     * can be treated just like any other Account.
     *
     * Accepts a JSON-formatted string, uses it to build an Account object,
     * and returns that object. This satisfies a promise made in the Jsonable
     * interface.
     *
     * @param string $json
     * @return \snuze\Reddit\Thing\Account
     * @see \snuze\Interfaces\Jsonable
     */
    public function fromJson(string $json) {

        /*
         * Reddit treats other users' accounts as Things (t2 kind), but doesn't
         * wrap "me" responses in a Thing structure. We'll massage the response
         * into the typical Thing format, so the parent can build an Account.
         */
        $jsonArray = [
            'kind' => 't2',
            'data' => json_decode($json, true),
        ];

        /* Call the parent to build an object from the massaged JSON */
        $obj = parent::fromJson(json_encode($jsonArray));

        /* Cache the incoming JSON; this may be used by the test suite */
        $this->_sourceJson = $json;

        /* Return the built object */
        return $obj;
    }

    /**
     * Overrides parent to perform specialized data handling. Reddit doesn't
     * return the typical "thing" data structure in response to this type of
     * request. Here, the JSON is massaged back into the original format.
     *
     * Returns a JSON-formatted string representing this Thing's properties.
     *
     * @return string
     */
    public function toJson(): string {

        $arr = [];
        foreach ($this->getPropertyTranslationMap() as $camel => $underscore) {
            $arr[$underscore] = $this->$camel;
        }

        return json_encode($arr,
                JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

}
