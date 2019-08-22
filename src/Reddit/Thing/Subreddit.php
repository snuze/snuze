<?php

declare(strict_types=1);

namespace snuze\Reddit\Thing;

/**
 * The Subreddit class represents the significant properties of a subreddit. An
 * attempt has been made to map all fields supplied by the API, regardless of
 * their utility (or lack thereof).
 *
 * Implementation warning: Array properties are currently populated as-is,
 * without any further processing. Calling their getters will return an array.
 * In the case of image dimensions ($bannerSize, $emojisCustomSize, etc.) the
 * array will contain integer width and height values. In other cases, it may
 * hold key/value pairs defining the property. This is subject to change in a
 * future major version, such that some other value(s) may be returned instead.
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
class Subreddit extends Thing
{

    /**
     * A regular expression used to test whether or not a subreddit name is
     * valid. Subreddit names must be between 3 and 21 characters long, contain
     * only alphanumerics and underscores, and can't start with an underscore.
     * There are several grandfathered exceptions, which are considered here.
     */
    const REGEX_VALID_NAME = '/^((?:[a-z0-9](?:[a-z0-9_]){2,20})|reddit\.com|ca|de|es|eu|fr|it|ja|nl|pl|ru)$/i';

    /*
     * These properties are documented alongside their accessors, below.
     */

    protected $accountsActive = 0;
    protected $accountsActiveIsFuzzed = false;
    protected $activeUserCount = 0;
    protected $advertiserCategory = '';
    protected $allOriginalContent = false;
    protected $allowDiscovery = true;
    protected $allowImages = true;
    protected $allowVideogifs = true;
    protected $allowVideos = true;
    protected $bannerBackgroundColor = '';
    protected $bannerBackgroundImage = '';
    protected $bannerImg = '';
    protected $bannerSize = null;
    protected $canAssignLinkFlair = false;
    protected $canAssignUserFlair = false;
    protected $coins = 0;
    protected $collapseDeletedComments = false;
    protected $collectionsEnabled = false;
    protected $commentScoreHideMins = 0;
    protected $communityIcon = '';
    protected $contentCategory = '';
    protected $description = '';
    protected $descriptionHtml = null;
    protected $disableContributorRequests = false;
    protected $displayName = null;
    protected $displayNamePrefixed = '';
    protected $emojisCustomSize = null;
    protected $emojisEnabled = false;
    protected $eventPostsEnabled = false;
    protected $freeFormReports = true;
    protected $hasMenuWidget = false;
    protected $headerImg = null;
    protected $headerSize = null;
    protected $headerTitle = null;
    protected $hideAds = false;
    protected $iconImg = '';
    protected $iconSize = null;
    protected $isEnrolledInNewModmail = null;
    protected $keyColor = '';
    protected $lang = '';
    protected $linkFlairEnabled = false;
    protected $linkFlairPosition = '';
    protected $mobileBannerImage = '';
    protected $name = '';
    protected $notificationLevel = null;
    protected $originalContentTagEnabled = false;
    protected $over18 = false;
    protected $primaryColor = '';
    protected $publicDescription = '';
    protected $publicDescriptionHtml = '';
    protected $publicTraffic = false;
    protected $quarantine = false;
    protected $restrictCommenting = false;
    protected $restrictPosting = true;
    protected $showMedia = false;
    protected $showMediaPreview = true;
    protected $spoilersEnabled = true;
    protected $submissionType = '';
    protected $submitLinkLabel = null;
    protected $submitText = '';
    protected $submitTextHtml = null;
    protected $submitTextLabel = null;
    protected $subredditType = '';
    protected $subscribers = 0;
    protected $suggestedCommentSort = null;
    protected $title = null;
    protected $url = '';
    protected $userCanFlairInSr = null;
    protected $userFlairBackgroundColor = null;
    protected $userFlairCssClass = null;
    protected $userFlairEnabledInSr = true;
    protected $userFlairPosition = '';
    protected $userFlairRichtext = [];
    protected $userFlairTemplateId = null;
    protected $userFlairText = null;
    protected $userFlairTextColor = null;
    protected $userFlairType = '';
    protected $userHasFavorited = false;
    protected $userIsBanned = false;
    protected $userIsContributor = false;
    protected $userIsModerator = false;
    protected $userIsMuted = false;
    protected $userIsSubscriber = false;
    protected $userSrFlairEnabled = null;
    protected $userSrThemeEnabled = false;
    protected $videostreamLinksCount = null;
    protected $whitelistStatus = null;
    protected $wikiEnabled = null;
    protected $wls = null;

    /**
     * Constructor.
     */
    public function __construct() {

        /* All Thing children must call parent ctor and set property names */
        parent::__construct(Thing::KIND_SUBREDDIT);
        $this->_propertyNames = array_keys(get_object_vars($this));
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /*
         * Override parent to work around an inconsistency in Reddit's field
         * naming scheme. Accounts have a field called over_18, but Subreddits
         * call it over18 with no underscore. Since no other field names in a
         * Subreddit contain numbers, digits can just be ignored here.
         */
        $this->_propertyTranslationRegex = '|([a-z])([A-Z])|';
    }

    /**
     * Get the (approximate) number of users interacting with this subreddit
     * over the past 15 minutes. Duplicate of getActiveUserCount().
     *
     * For some unknown reason, Reddit occasionally returns this as an empty
     * array instead of an integer. It's not reproducible; you can ask for the
     * same subreddit 500 times, and get an integer 499 times, and an empty
     * array once. In cases where this occurs, this method will return 0.
     *
     * @return int
     */
    public function getAccountsActive(): int {
        return $this->accountsActive;
    }

    /**
     * Set the (approximate) number of users interacting with this subreddit
     * over the past 15 minutes.
     *
     * For some unknown reason, Reddit occasionally returns this as an empty
     * array instead of an integer. It's not reproducible; you can ask for the
     * same subreddit 500 times, and get an integer 499 times, and an empty
     * array once. As a kludge, we accept any type here and cast it to an int;
     * the randomly-encountered empty array will translate to 0.
     *
     * @param mixed $accountsActive
     * @return $this
     */
    protected function setAccountsActive($accountsActive = null) {

        $this->accountsActive = (int) $accountsActive;
        return $this;
    }

    /**
     * Get whether or not accountsActive and activeUserCount values are slightly
     * adjusted to mitigate statistical inference attacks
     *
     * @return bool
     */
    public function getAccountsActiveIsFuzzed(): bool {
        return $this->accountsActiveIsFuzzed;
    }

    /**
     * Set whether or not accountsActive and activeUserCount values are slightly
     * adjusted to mitigate statistical inference attacks
     *
     * @param bool $accountsActiveIsFuzzed
     * @return $this
     */
    protected function setAccountsActiveIsFuzzed(bool $accountsActiveIsFuzzed) {
        $this->accountsActiveIsFuzzed = $accountsActiveIsFuzzed;
        return $this;
    }

    /**
     * Get the (approximate) number of users interacting with this subreddit.
     * Duplicate of getAccountsActive().
     *
     * For some unknown reason, Reddit occasionally returns this as an empty
     * array instead of an integer. It's not reproducible; you can ask for the
     * same subreddit 500 times, and get an integer 499 times, and an empty
     * array once. In cases where this occurs, this method will return 0.
     *
     * @return int
     */
    public function getActiveUserCount(): int {
        return $this->activeUserCount;
    }

    /**
     * Set the (approximate) number of users interacting with this subreddit.
     *
     * For some unknown reason, Reddit occasionally returns this as an empty
     * array instead of an integer. It's not reproducible; you can ask for the
     * same subreddit 500 times, and get an integer 499 times, and an empty
     * array once. As a kludge, we accept any type here and cast it to an int;
     * the randomly-encountered empty array will translate to 0.
     *
     * @param mixed $activeUserCount
     * @return $this
     */
    protected function setActiveUserCount($activeUserCount) {
        $this->activeUserCount = (int) $activeUserCount;
        return $this;
    }

    /**
     * Get the advertiser category, if any, this subreddit belongs to.
     *
     * @return string
     */
    public function getAdvertiserCategory(): string {
        return $this->advertiserCategory;
    }

    /**
     * Set the advertiser category, if any, this subreddit belongs to.
     *
     * @param string $advertiserCategory
     * @return $this
     */
    protected function setAdvertiserCategory(string $advertiserCategory) {
        $this->advertiserCategory = $advertiserCategory;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "mark all posts in this
     * subreddit as Original Content (OC) on the desktop redesign" option enabled
     *
     * @return bool
     */
    public function getAllOriginalContent(): bool {
        return $this->allOriginalContent;
    }

    /**
     * Set whether or not this subreddit has the "mark all posts in this
     * subreddit as Original Content (OC) on the desktop redesign" option enabled
     *
     * @param bool $allOriginalContent
     * @return $this
     */
    protected function setAllOriginalContent(bool $allOriginalContent) {
        $this->allOriginalContent = $allOriginalContent;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "allow this subreddit to be
     * exposed to users who have shown intent or interest through discovery and
     * onboarding" option enabled
     *
     * @return bool
     */
    public function getAllowDiscovery(): bool {
        return $this->allowDiscovery;
    }

    /**
     * Set whether or not this subreddit has the "allow this subreddit to be
     * exposed to users who have shown intent or interest through discovery and
     * onboarding" option enabled
     *
     * @param bool $allowDiscovery
     * @return $this
     */
    protected function setAllowDiscovery(bool $allowDiscovery) {
        $this->allowDiscovery = $allowDiscovery;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "allow image uploads and links
     * to image hosting sites" option enabled
     *
     * @return bool
     */
    public function getAllowImages(): bool {
        return $this->allowImages;
    }

    /**
     * Set whether or not this subreddit has the "allow image uploads and links
     * to image hosting sites" option enabled
     *
     * @param bool $allowImages
     * @return $this
     */
    protected function setAllowImages(bool $allowImages) {
        $this->allowImages = $allowImages;
        return $this;
    }

    /**
     * Get whether or not videogifs are allowed in this subreddit, whatever
     * videogifs are.
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getAllowVideogifs(): bool {
        return $this->allowVideogifs;
    }

    /**
     * Set whether or not videogifs are allowed in this subreddit, whatever
     * videogifs are.
     *
     * @param bool $allowVideogifs
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setAllowVideogifs(bool $allowVideogifs) {
        $this->allowVideogifs = $allowVideogifs;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "allow video uploads" option
     * enabled
     *
     * @return bool
     */
    public function getAllowVideos(): bool {
        return $this->allowVideos;
    }

    /**
     * Set whether or not this subreddit has the "allow video uploads" option
     * enabled
     *
     * @param bool $allowVideos
     * @return $this
     */
    protected function setAllowVideos(bool $allowVideos) {
        $this->allowVideos = $allowVideos;
        return $this;
    }

    /**
     * Get the HTML hex color code for this subreddit's banner background, if any
     *
     * @return string
     */
    public function getBannerBackgroundColor(): string {
        return $this->bannerBackgroundColor;
    }

    /**
     * Set the HTML hex color code for this subreddit's banner background, if any
     *
     * @param string $bannerBackgroundColor
     * @return $this
     */
    protected function setBannerBackgroundColor(string $bannerBackgroundColor) {
        $this->bannerBackgroundColor = $bannerBackgroundColor;
        return $this;
    }

    /**
     * Get the URL to this subreddit's banner background image, if any. This is
     * the image that displays on the desktop site.
     *
     * @return string
     */
    public function getBannerBackgroundImage(): string {
        return $this->bannerBackgroundImage;
    }

    /**
     * Set the URL to this subreddit's banner background image, if any. This is
     * the image that displays on the desktop site.
     *
     * @param string $bannerBackgroundImage
     * @return $this
     */
    protected function setBannerBackgroundImage(string $bannerBackgroundImage) {
        $this->bannerBackgroundImage = $bannerBackgroundImage;
        return $this;
    }

    /**
     * Get the URL to this subreddit's banner image, if any.
     *
     * @return string
     */
    public function getBannerImg(): string {
        return $this->bannerImg;
    }

    /**
     * Set the URL to this subreddit's banner image, if any
     *
     * @param string $bannerImg
     * @return $this
     */
    protected function setBannerImg(string $bannerImg) {
        $this->bannerImg = $bannerImg;
        return $this;
    }

    /**
     * Get the banner image dimensions. If set, this will be an array containing
     * two integer values.
     *
     * @return array|null
     */
    public function getBannerSize(): ?array {
        return $this->bannerSize;
    }

    /**
     * Set the banner image dimensions. This should be an array containing
     * two integer values.
     *
     * @param array $bannerSize
     * @return $this
     */
    protected function setBannerSize(array $bannerSize = null) {
        $this->bannerSize = $bannerSize;
        return $this;
    }

    /**
     * Get whether or not users can assign flair to their own links in this
     * subreddit
     *
     * @return bool
     */
    public function getCanAssignLinkFlair(): bool {
        return $this->canAssignLinkFlair;
    }

    /**
     * Set whether or not users can assign flair to their own links in this
     * subreddit
     *
     * @param bool $canAssignLinkFlair
     * @return $this
     */
    protected function setCanAssignLinkFlair(bool $canAssignLinkFlair) {
        $this->canAssignLinkFlair = $canAssignLinkFlair;
        return $this;
    }

    /**
     * Get whether or not this subreddit allows users to assign flair to
     * themselves
     *
     * @return bool
     */
    public function getCanAssignUserFlair(): bool {
        return $this->canAssignUserFlair;
    }

    /**
     * Set whether or not this subreddit allows users to assign flair to
     * themselves
     *
     * @param bool $canAssignUserFlair
     * @return $this
     */
    protected function setCanAssignUserFlair(bool $canAssignUserFlair) {
        $this->canAssignUserFlair = $canAssignUserFlair;
        return $this;
    }

    /**
     * Get this subreddit's Community Awards coin balance. If the subreddit
     * has no Community Awards coins, this will be 0.
     *
     * @return int
     */
    public function getCoins(): int {
        return $this->coins;
    }

    /**
     * Set this subreddit's Community Awards coin balance.
     *
     * @param int $coins
     * @return $this
     */
    public function setCoins(int $coins) {
        $this->coins = $coins;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "collapse deleted and removed
     * comments" option enabled
     *
     * @return bool
     */
    public function getCollapseDeletedComments(): bool {
        return $this->collapseDeletedComments;
    }

    /**
     * Get whether or not this subreddit supports links that are collections
     * of other links
     *
     * @return bool|null
     */
    public function getCollectionsEnabled(): bool {
        return $this->collectionsEnabled;
    }

    /**
     * Set whether or not this subreddit supports links that are collections
     * of other links
     *
     * @param bool $collectionsEnabled
     * @return $this
     */
    public function setCollectionsEnabled(bool $collectionsEnabled) {
        $this->collectionsEnabled = $collectionsEnabled;
        return $this;
    }

    /**
     * Set whether or not this subreddit has the "collapse deleted and removed
     * comments" option enabled
     *
     * @param bool $collapseDeletedComments
     * @return $this
     */
    protected function setCollapseDeletedComments(bool $collapseDeletedComments) {
        $this->collapseDeletedComments = $collapseDeletedComments;
        return $this;
    }

    /**
     * Get the "Minutes to hide comment scores" value for this subreddit
     *
     * @return int
     */
    public function getCommentScoreHideMins(): int {
        return $this->commentScoreHideMins;
    }

    /**
     * Set the "Minutes to hide comment scores" value for this subreddit
     *
     * @param int $commentScoreHideMins
     * @return $this
     */
    protected function setCommentScoreHideMins(int $commentScoreHideMins = 0) {
        $this->commentScoreHideMins = $commentScoreHideMins;
        return $this;
    }

    /**
     * Get the URL to this subreddit's community icon image, if any
     *
     * @return string
     */
    public function getCommunityIcon(): string {
        return $this->communityIcon;
    }

    /**
     * Set the URL to this subreddit's community icon image, if any
     *
     * @param string $communityIcon
     * @return $this
     */
    protected function setCommunityIcon(string $communityIcon) {
        $this->communityIcon = $communityIcon;
        return $this;
    }

    /**
     * Get this subreddit's assigned content category, if any
     *
     * @return string
     */
    public function getContentCategory(): string {
        return $this->contentCategory;
    }

    /**
     * Set this subreddit's assigned content category, if any
     *
     * @param string $contentCategory
     * @return $this
     */
    protected function setContentCategory(string $contentCategory) {
        $this->contentCategory = $contentCategory;
        return $this;
    }

    /**
     * Get this subreddit's description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Set this subreddit's description
     *
     * @param string $description
     * @return $this
     */
    protected function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get this subreddit's description as HTML instead of text
     *
     * @return string|null
     */
    public function getDescriptionHtml(): ?string {
        return $this->descriptionHtml;
    }

    /**
     * Set this subreddit's description as HTML instead of text
     *
     * @param string $descriptionHtml
     * @return $this
     */
    protected function setDescriptionHtml(string $descriptionHtml = null) {
        $this->descriptionHtml = $descriptionHtml;
        return $this;
    }

    /**
     * Get whether or not this subreddit forbids public requests for status as
     * an approved submitter. (This may be true even if the subreddit is
     * currently public.)
     *
     * @return bool
     */
    public function getDisableContributorRequests(): bool {
        return $this->disableContributorRequests;
    }

    /**
     * Set whether or not this subreddit forbids public requests for status as
     * an approved submitter.
     *
     * @param bool $disableContributorRequests
     * @return $this
     */
    protected function setDisableContributorRequests(bool $disableContributorRequests) {
        $this->disableContributorRequests = $disableContributorRequests;
        return $this;
    }

    /**
     * Get the undecorated display name of the subreddit e.g. "funny"
     *
     * @return string
     */
    public function getDisplayName(): string {
        return $this->displayName;
    }

    /**
     * Set the display name of the subreddit e.g. "funny". The supplied name
     * is checked against known subreddit name restrictions.
     *
     * @param string $displayName
     * @throws \snuze\Exception\ArgumentException
     */
    protected function setDisplayName(string $displayName) {

        /* Check for a valid subreddit name  */
        if (!preg_match(self::REGEX_VALID_NAME, $displayName)) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid subreddit name');
        }

        $this->displayName = $displayName;
    }

    /**
     * Get the subreddit name with its relative path e.g. "r/funny", or
     * "u/joe" for user profile subreddits
     *
     * @return string
     */
    public function getDisplayNamePrefixed(): string {
        return $this->displayNamePrefixed;
    }

    /**
     * Set the subreddit name with its relative path e.g. "r/funny", or
     * "u/joe" for user profile subreddits
     *
     * @param string $displayNamePrefixed
     * @return $this
     */
    protected function setDisplayNamePrefixed(string $displayNamePrefixed) {
        $this->displayNamePrefixed = $displayNamePrefixed;
        return $this;
    }

    /**
     * Get the emoji image dimensions. If set, this will be an array containing
     * two integer values.
     *
     * @return array|null
     */
    public function getEmojisCustomSize(): ?array {
        return $this->emojisCustomSize;
    }

    /**
     * Set the emoji image dimensions. This should be an array containing
     * two integer values.
     *
     * @param array $emojisCustomSize
     * @return $this
     */
    protected function setEmojisCustomSize(array $emojisCustomSize = null) {
        $this->emojisCustomSize = $emojisCustomSize;
        return $this;
    }

    /**
     * Get whether or not custom emojis are enabled for this subreddit
     *
     * @return bool
     */
    public function getEmojisEnabled(): bool {
        return $this->emojisEnabled;
    }

    /**
     * Set whether or not custom emojis are enabled for this subreddit
     *
     * @param bool $emojisEnabled
     * @return $this
     */
    protected function setEmojisEnabled(bool $emojisEnabled) {
        $this->emojisEnabled = $emojisEnabled;
        return $this;
    }

    /**
     * Get whether or not this subreddit supports links that are event posts
     *
     * @return bool
     */
    public function getEventPostsEnabled(): bool {
        return $this->eventPostsEnabled;
    }

    /**
     * Set whether or not this subreddit supports links that are event posts
     *
     * @param bool $eventPostsEnabled
     * @return $this
     */
    public function setEventPostsEnabled(bool $eventPostsEnabled) {
        $this->eventPostsEnabled = $eventPostsEnabled;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "allow free-form reports by
     * users" option enabled
     *
     * @return bool
     */
    public function getFreeFormReports(): bool {
        return $this->freeFormReports;
    }

    /**
     * Set whether or not this subreddit has the "allow free-form reports by
     * users" option enabled
     *
     * @param bool $freeFormReports
     * @return $this
     */
    protected function setFreeFormReports(bool $freeFormReports) {
        $this->freeFormReports = $freeFormReports;
        return $this;
    }

    /**
     * Get whether or not this subreddit has custom menu tabs defined by a
     * moderator.
     *
     * @return bool
     */
    public function getHasMenuWidget(): bool {
        return $this->hasMenuWidget;
    }

    /**
     * Set whether or not this subreddit has custom menu tabs defined by a
     * moderator.
     *
     * @param bool $hasMenuWidget
     * @return $this
     */
    protected function setHasMenuWidget(bool $hasMenuWidget) {
        $this->hasMenuWidget = $hasMenuWidget;
        return $this;
    }

    /**
     * Get the URL to this subreddit's header image, if any. This is the
     * image that appears in place of Snoo on the old style Reddit.
     *
     * @return string|null
     */
    public function getHeaderImg(): ?string {
        return $this->headerImg;
    }

    /**
     * Set the URL to this subreddit's header image, if any. This is the
     * image that appears in place of Snoo on the old style Reddit.
     *
     * @param string $headerImg
     * @return $this
     */
    protected function setHeaderImg(string $headerImg = null) {
        $this->headerImg = $headerImg;
        return $this;
    }

    /**
     * Get the header image dimensions. If set, this will be an array containing
     * two integer values.
     *
     * @return array|null
     */
    public function getHeaderSize(): ?array {
        return $this->headerSize;
    }

    /**
     * Set the header image dimensions. This should be an array containing
     * two integer values.
     *
     * @param array $headerSize
     * @return $this
     */
    protected function setHeaderSize(array $headerSize = null) {
        $this->headerSize = $headerSize;
        return $this;
    }

    /**
     * Get the title/alt text of this subreddit's header image, if any
     *
     * @return string|null
     */
    public function getHeaderTitle(): ?string {
        return $this->headerTitle;
    }

    /**
     * Set the title/alt text of this subreddit's header image, if any
     *
     * @param string $headerTitle
     * @return $this
     */
    protected function setHeaderTitle(string $headerTitle = null) {
        $this->headerTitle = $headerTitle;
        return $this;
    }

    /**
     * Get whether or not ads have been administratively suppressed in this
     * subreddit
     *
     * @return bool
     */
    public function getHideAds(): bool {
        return $this->hideAds;
    }

    /**
     * Set whether or not ads have been administratively suppressed in this
     * subreddit
     *
     * @param bool $hideAds
     * @return $this
     */
    protected function setHideAds(bool $hideAds) {
        $this->hideAds = $hideAds;
        return $this;
    }

    /**
     * Get the URL to this subreddit's icon image, if any. This icon is
     * displayed in the "Community Details" portion of the sidebar and in
     * public "card" style listings, both on the redesign interface.
     *
     * @return string
     */
    public function getIconImg(): string {
        return $this->iconImg;
    }

    /**
     * Set the URL to this subreddit's icon image, if any. This icon is
     * displayed in the "Community Details" portion of the sidebar and in
     * public "card" style listings, both on the redesign interface.
     *
     * @param string $iconImg
     * @return $this
     */
    protected function setIconImg(string $iconImg) {
        $this->iconImg = $iconImg;
        return $this;
    }

    /**
     * Get the icon image dimensions. If set, this will be an array containing
     * two integer values.
     *
     * @return array|null
     */
    public function getIconSize(): ?array {
        return $this->iconSize;
    }

    /**
     * Set the icon image dimensions. This should be an array containing
     * two integer values.
     *
     * @param array $iconSize
     * @return $this
     */
    protected function setIconSize(array $iconSize = null) {
        $this->iconSize = $iconSize;
        return $this;
    }

    /**
     * Get whether or not this subreddit uses the new modmail system.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return bool|null
     */
    public function getIsEnrolledInNewModmail(): ?bool {
        return $this->isEnrolledInNewModmail;
    }

    /**
     * Set whether or not this subreddit uses the new modmail system
     *
     * @param bool $isEnrolledInNewModmail
     * @return $this
     */
    protected function setIsEnrolledInNewModmail(bool $isEnrolledInNewModmail = null) {
        $this->isEnrolledInNewModmail = $isEnrolledInNewModmail;
        return $this;
    }

    /**
     * Get the HTML hex color code for this subreddit's general theme color,
     * if any is set. Corresponds to the "used as a thematic color for your
     * subreddit on mobile" option.
     *
     * @return string
     */
    public function getKeyColor(): string {
        return $this->keyColor;
    }

    /**
     * Set the HTML hex color code for this subreddit's general theme color.
     * Corresponds to the "used as a thematic color for your subreddit on
     * mobile" option.
     *
     * @param string $keyColor
     * @return $this
     */
    protected function setKeyColor(string $keyColor) {
        $this->keyColor = $keyColor;
        return $this;
    }

    /**
     * Get the language code for this subreddit, if any; e.g. "en" or "pt-pt"
     *
     * @return string
     */
    public function getLang(): string {
        return $this->lang;
    }

    /**
     * Set the language code for this subreddit, if any; e.g. "en" or "pt-pt"
     *
     * @param string $lang
     * @return $this
     */
    protected function setLang(string $lang) {
        $this->lang = $lang;
        return $this;
    }

    /**
     * Get whether or not link flair is supported in this subreddit
     *
     * @return bool
     */
    public function getLinkFlairEnabled(): bool {
        return $this->linkFlairEnabled;
    }

    /**
     * Set whether or not link flair is supported in this subreddit
     *
     * @param bool $linkFlairEnabled
     * @return $this
     */
    protected function setLinkFlairEnabled(bool $linkFlairEnabled) {
        $this->linkFlairEnabled = $linkFlairEnabled;
        return $this;
    }

    /**
     * Get the CSS position of a link's flair in this subreddit, if any,
     * relative to the link, e.g. "left", "right"
     *
     * @return string
     */
    public function getLinkFlairPosition(): string {
        return $this->linkFlairPosition;
    }

    /**
     * Set the CSS position of a link's flair in this subreddit, if any,
     * relative to the link, e.g. "left", "right"
     *
     * @param string $linkFlairPosition
     * @return $this
     */
    protected function setLinkFlairPosition(string $linkFlairPosition) {
        $this->linkFlairPosition = $linkFlairPosition;
        return $this;
    }

    /**
     * Get the URL to this subreddit's mobile banner image, if any
     *
     * @return string
     */
    public function getMobileBannerImage(): string {
        return $this->mobileBannerImage;
    }

    /**
     * Set the URL to this subreddit's mobile banner image, if any
     *
     * @param string $mobileBannerImage
     * @return $this
     */
    protected function setMobileBannerImage(string $mobileBannerImage) {
        $this->mobileBannerImage = $mobileBannerImage;
        return $this;
    }

    /**
     * Returns the Reddit fullname identifier of this subreddit e.g. "t5_2qh33".
     * If you want the subreddit display name e.g. "funny", use getDisplayName()
     * instead.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set the Reddit fullname identifier of this subreddit e.g. "t5_2qh33".
     * If you want the subreddit display name e.g. "funny", use setDisplayName()
     * instead.
     *
     * @param string $name
     * @return $this
     */
    protected function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the notification level (?)
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     * @todo verify what exactly this corresponds to
     */
    public function getNotificationLevel(): ?string {
        return $this->notificationLevel;
    }

    /**
     * Set the notification level (?)
     *
     * @param string $notificationLevel
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setNotificationLevel(string $notificationLevel = null) {
        $this->notificationLevel = $notificationLevel;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "enable marking posts as
     * Original Content (OC) on the desktop redesign" option enabled
     *
     * @return bool
     */
    public function getOriginalContentTagEnabled(): bool {
        return $this->originalContentTagEnabled;
    }

    /**
     * Set whether or not this subreddit has the "enable marking posts as
     * Original Content (OC) on the desktop redesign" option enabled
     *
     * @param bool $originalContentTagEnabled
     * @return $this
     */
    protected function setOriginalContentTagEnabled(bool $originalContentTagEnabled) {
        $this->originalContentTagEnabled = $originalContentTagEnabled;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "viewers must be over eighteen
     * years old" option enabled
     *
     * @return bool
     */
    public function getOver18(): bool {
        return $this->over18;
    }

    /**
     * Set whether or not this subreddit has the "viewers must be over eighteen
     * years old" option enabled
     *
     * @param bool $over18
     * @return $this
     */
    protected function setOver18(bool $over18) {
        $this->over18 = $over18;
        return $this;
    }

    /**
     * Get the HTML hex color code for this subreddit's primary color, if any.
     * This corresponds to the "Base" theme color in the redesign subreddit
     * manager interface.
     *
     * @return string
     */
    public function getPrimaryColor(): string {
        return $this->primaryColor;
    }

    /**
     * Set the HTML hex color code for this subreddit's primary color, if any.
     * This corresponds to the "Base" theme color in the redesign subreddit
     * manager interface.
     *
     * @param string $primaryColor
     * @return $this
     */
    protected function setPrimaryColor(string $primaryColor) {
        $this->primaryColor = $primaryColor;
        return $this;
    }

    /**
     * Get the external description for this subreddit, if any. This is used in
     * <meta name="description"> tags, and in subreddit search results; it's
     * distinct from the sidebar description, which can be found using
     * getDescription()
     *
     * @return string
     * @see getDescription()
     */
    public function getPublicDescription(): string {
        return $this->publicDescription;
    }

    /**
     * Set the external description for this subreddit, if any. This is used in
     * <meta name="description"> tags, and in subreddit search results; it's
     * distinct from the sidebar description, which can be set using
     * getDescription()
     *
     * @param string $publicDescription
     * @return $this
     * @see setDescription()
     */
    protected function setPublicDescription(string $publicDescription) {
        $this->publicDescription = $publicDescription;
        return $this;
    }

    /**
     * Get the external description for this subreddit, if any, as HTML instead
     * of text
     *
     * @return string
     */
    public function getPublicDescriptionHtml(): string {
        return $this->publicDescriptionHtml;
    }

    /**
     * Set the external description for this subreddit, if any, as HTML instead
     * of text
     *
     * @param string $publicDescriptionHtml
     * @return $this
     */
    protected function setPublicDescriptionHtml(string $publicDescriptionHtml) {
        $this->publicDescriptionHtml = $publicDescriptionHtml;
        return $this;
    }

    /**
     * Get whether or not this subreddit exposes its traffic stats to the public
     *
     * @return bool
     */
    public function getPublicTraffic(): bool {
        return $this->publicTraffic;
    }

    /**
     * Set whether or not this subreddit exposes its traffic stats to the public
     *
     * @param bool $publicTraffic
     * @return $this
     */
    protected function setPublicTraffic(bool $publicTraffic) {
        $this->publicTraffic = $publicTraffic;
        return $this;
    }

    /**
     * Get whether or not this subreddit is quarantined
     *
     * @return bool
     */
    public function getQuarantine(): bool {
        return $this->quarantine;
    }

    /**
     * Set whether or not this subreddit is quarantined
     *
     * @param bool $quarantine
     * @return $this
     */
    protected function setQuarantine(bool $quarantine) {
        $this->quarantine = $quarantine;
        return $this;
    }

    /**
     * Get whether or not commenting is restricted (?)
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getRestrictCommenting(): bool {
        return $this->restrictCommenting;
    }

    /**
     * Set whether or not commenting is restricted (?)
     *
     * @param bool $restrictCommenting
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setRestrictCommenting(bool $restrictCommenting) {
        $this->restrictCommenting = $restrictCommenting;
        return $this;
    }

    /**
     * Get whether or not submissions are restricted (?)
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getRestrictPosting(): bool {
        return $this->restrictPosting;
    }

    /**
     * Set whether or not submissions are restricted (?)
     * @param bool $restrictPosting
     * @return $this
     */
    protected function setRestrictPosting(bool $restrictPosting) {
        $this->restrictPosting = $restrictPosting;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "show thumbnail images of
     * content" option enabled
     *
     * @return bool
     */
    public function getShowMedia(): bool {
        return $this->showMedia;
    }

    /**
     * Set whether or not this subreddit has the "show thumbnail images of
     * content" option enabled
     *
     * @param bool $showMedia
     * @return $this
     */
    protected function setShowMedia(bool $showMedia) {
        $this->showMedia = $showMedia;
        return $this;
    }

    /**
     * Get whether or not this subreddit has the "expand media previews on
     * comments pages" option enabled
     *
     * @return bool
     */
    public function getShowMediaPreview(): bool {
        return $this->showMediaPreview;
    }

    /**
     * Set whether or not this subreddit has the "expand media previews on
     * comments pages" option enabled
     *
     * @param bool $showMediaPreview
     * @return $this
     */
    protected function setShowMediaPreview(bool $showMediaPreview) {
        $this->showMediaPreview = $showMediaPreview;
        return $this;
    }

    /**
     * Get whether or not this subreddit allows marking links as spoilers
     *
     * @return bool
     */
    public function getSpoilersEnabled(): bool {
        return $this->spoilersEnabled;
    }

    /**
     * Set whether or not this subreddit allows marking links as spoilers
     *
     * @param bool $spoilersEnabled
     * @return $this
     */
    protected function setSpoilersEnabled(bool $spoilersEnabled) {
        $this->spoilersEnabled = $spoilersEnabled;
        return $this;
    }

    /**
     * Get the submission type allowed in this subreddit. Will usually be one of
     * ['any', 'link', 'self'], but some banned and employee-only subreddits
     * may return an empty string
     *
     * @return string
     */
    public function getSubmissionType(): string {
        return $this->submissionType;
    }

    /**
     * Set the submission type allowed in this subreddit
     *
     * @param string $submissionType
     * @return $this
     */
    protected function setSubmissionType(string $submissionType) {
        $this->submissionType = $submissionType;
        return $this;
    }

    /**
     * Get the text configured for this subreddit's "Custom label for submit
     * link button" option, if any
     *
     * @return string|null
     */
    public function getSubmitLinkLabel(): ?string {
        return $this->submitLinkLabel;
    }

    /**
     * Set the text configured for this subreddit's "Custom label for submit
     * link button" option, if any
     *
     * @param string $submitLinkLabel
     * @return $this
     */
    protected function setSubmitLinkLabel(string $submitLinkLabel = null) {
        $this->submitLinkLabel = $submitLinkLabel;
        return $this;
    }

    /**
     * Get this subreddit's configured "text to show on submission page,"
     * if any
     *
     * @return string
     */
    public function getSubmitText(): string {
        return $this->submitText;
    }

    /**
     * Set this subreddit's configured "text to show on submission page,"
     * if any
     *
     * @param string $submitText
     * @return $this
     */
    protected function setSubmitText(string $submitText) {
        $this->submitText = $submitText;
        return $this;
    }

    /**
     * Get this subreddit's custom "submit text post" label, if any, as HTML
     * instead of text
     *
     * @return string|null
     */
    public function getSubmitTextHtml(): ?string {
        return $this->submitTextHtml;
    }

    /**
     * Get this subreddit's custom "submit text post" label, if any, as HTML
     * instead of text
     *
     * @param string $submitTextHtml
     * @return $this
     */
    protected function setSubmitTextHtml(string $submitTextHtml = null) {
        $this->submitTextHtml = $submitTextHtml;
        return $this;
    }

    /**
     * Get the text configured for this subreddit's "Custom label for submit
     * text post button" option, if any
     *
     * @return string|null
     */
    public function getSubmitTextLabel(): ?string {
        return $this->submitTextLabel;
    }

    /**
     * Set the text configured for this subreddit's "Custom label for submit
     * text post button" option, if any
     *
     * @param string $submitTextLabel
     * @return $this
     */
    protected function setSubmitTextLabel(string $submitTextLabel = null) {
        $this->submitTextLabel = $submitTextLabel;
        return $this;
    }

    /**
     * Get the type of access control enforced on this subreddit. This should be
     * one of ['public', 'restricted', 'private', 'employees_only']
     *
     * @return string
     */
    public function getSubredditType(): string {
        return $this->subredditType;
    }

    /**
     * Set the type of access control enforced on this subreddit. This should be
     * one of ['public', 'restricted', 'private', 'employees_only']
     *
     * @param string $subredditType
     * @return $this
     */
    protected function setSubredditType(string $subredditType) {
        $this->subredditType = $subredditType;
        return $this;
    }

    /**
     * Get the number of accounts subscribed to this subreddit
     *
     * @return int
     */
    public function getSubscribers(): int {
        return $this->subscribers;
    }

    /**
     * Set the number of accounts subscribed to this subreddit
     *
     * @param int $subscribers
     * @return $this
     */
    protected function setSubscribers(int $subscribers) {
        $this->subscribers = $subscribers;
        return $this;
    }

    /**
     * Get the suggested comment sort order for this subreddit, if one has
     * been set
     *
     * @return string|null
     */
    public function getSuggestedCommentSort(): ?string {
        return $this->suggestedCommentSort;
    }

    /**
     * Set the suggested comment sort order for this subreddit
     *
     * @param string $suggestedCommentSort
     * @return $this
     */
    protected function setSuggestedCommentSort(string $suggestedCommentSort = null) {
        $this->suggestedCommentSort = $suggestedCommentSort;
        return $this;
    }

    /**
     * Get this subreddit's title
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Set this subreddit's title
     *
     * @param string $title
     * @return $this
     */
    protected function setTitle(string $title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the subreddit name with its fully-qualified relative path
     * e.g. "/r/funny", or "/u/joe" for user profile subreddits
     *
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Set the subreddit name with its fully-qualified relative path
     * e.g. "/r/funny", or "/u/joe" for user profile subreddits
     *
     * @param string $url
     * @return $this
     */
    protected function setUrl(string $url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user is allowed to set
     * flair in this subreddit. This will be true if the "Allow users to assign
     * their own" user flair option is enabled, *or* if the user is a moderator
     * of this subreddit with the "flair" permission. Otherwise, returns null.
     *
     * @return bool|null
     */
    public function getUserCanFlairInSr(): ?bool {
        return $this->userCanFlairInSr;
    }

    /**
     * Set whether or not the currently authenticated user is allowed to set
     * flair in this subreddit
     *
     * @param bool $userCanFlairInSr
     * @return $this
     */
    protected function setUserCanFlairInSr(bool $userCanFlairInSr = null) {
        $this->userCanFlairInSr = $userCanFlairInSr;
        return $this;
    }

    /**
     * Get the HTML hex color code for the currently authenticated user's user
     * flair background in this subreddit, if any
     *
     * @return string|null
     */
    public function getUserFlairBackgroundColor(): ?string {
        return $this->userFlairBackgroundColor;
    }

    /**
     * Set the HTML hex color code for the currently authenticated user's
     * user flair background in this subreddit, if any
     *
     * @param string $userFlairBackgroundColor
     * @return $this
     */
    protected function setUserFlairBackgroundColor(string $userFlairBackgroundColor
            = null) {
        $this->userFlairBackgroundColor = $userFlairBackgroundColor;
        return $this;
    }

    /**
     * Get the CSS class corresponding to the currently authenticated user's
     * user flair on this subreddit
     *
     * @return string|null
     * @todo verify what exactly this corresponds to
     */
    public function getUserFlairCssClass(): ?string {
        return $this->userFlairCssClass;
    }

    /**
     * Set the CSS class corresponding to the currently authenticated user's
     * user flair on this subreddit
     *
     * @param string $userFlairCssClass
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setUserFlairCssClass(string $userFlairCssClass = null) {
        $this->userFlairCssClass = $userFlairCssClass;
        return $this;
    }

    /**
     * Get whether or not user flair is enabled in this subreddit. This applies
     * to the subreddit generally, not to the currently authenticated user.
     *
     * @return bool|null
     */
    public function getUserFlairEnabledInSr(): ?bool {
        return $this->userFlairEnabledInSr;
    }

    /**
     * Set whether or not user flair is enabled in this subreddit. This applies
     * to the subreddit generally, not to the currently authenticated user.
     *
     * @param bool $userFlairEnabledInSr
     * @return $this
     */
    protected function setUserFlairEnabledInSr(bool $userFlairEnabledInSr = null) {
        $this->userFlairEnabledInSr = $userFlairEnabledInSr;
        return $this;
    }

    /**
     * Get the CSS position of the currently authenticated user's flair in this
     * subreddit, if any, relative to their username, e.g. "left", "right"
     *
     * @return string
     */
    public function getUserFlairPosition(): string {
        return $this->userFlairPosition;
    }

    /**
     * Set the CSS position of the currently authenticated user's flair in this
     * subreddit, if any, relative to their username, e.g. "left", "right"
     *
     * @param string $userFlairPosition
     * @return $this
     */
    protected function setUserFlairPosition(string $userFlairPosition) {
        $this->userFlairPosition = $userFlairPosition;
        return $this;
    }

    /**
     * Get the array containing the elements that define the currently
     * authenticated user's flair in this subreddit, if any.
     *
     * @return array
     */
    public function getUserFlairRichtext(): array {
        return $this->userFlairRichtext;
    }

    /**
     * Set the array containing the elements that define the currently
     * authenticated user's flair in this subreddit, if any.
     *
     * @param array $userFlairRichtext
     * @return $this
     */
    protected function setUserFlairRichtext(array $userFlairRichtext) {
        $this->userFlairRichtext = $userFlairRichtext;
        return $this;
    }

    /**
     * Get the 36-character UUID of the currently authenticated user's flair
     * template in this subreddit, if any
     *
     * @return string|null
     */
    public function getUserFlairTemplateId(): ?string {
        return $this->userFlairTemplateId;
    }

    /**
     * Set the 36-character UUID of the currently authenticated user's flair
     * template in this subreddit, if any
     *
     * @param string $userFlairTemplateId
     * @return $this
     */
    protected function setUserFlairTemplateId(string $userFlairTemplateId = null) {
        $this->userFlairTemplateId = $userFlairTemplateId;
        return $this;
    }

    /**
     * Get the currently authenticated user's flair text in this subreddit, if any
     *
     * @return string|null
     */
    public function getUserFlairText(): ?string {
        return $this->userFlairText;
    }

    /**
     * Set the currently authenticated user's flair text in this subreddit, if any
     *
     * @param string $userFlairText
     * @return $this
     */
    protected function setUserFlairText(string $userFlairText = null) {
        $this->userFlairText = $userFlairText;
        return $this;
    }

    /**
     * Get the color, either "dark" or "light", of the currently authenticated
     * user's user flair text in this subreddit, if any
     *
     * @return string|null
     */
    public function getUserFlairTextColor(): ?string {
        return $this->userFlairTextColor;
    }

    /**
     * Set the color, either "dark" or "light", of the currently authenticated
     * user's user flair text in this subreddit, if any
     *
     * @param string $userFlairTextColor Must be "dark" or "light"
     * @return $this
     */
    protected function setUserFlairTextColor(string $userFlairTextColor = null) {
        $this->userFlairTextColor = $userFlairTextColor;
        return $this;
    }

    /**
     * Get the currently authenticated user's flair type for this subreddit,
     * if any; e.g. "text" or "richtext"
     *
     * @return string
     */
    public function getUserFlairType(): string {
        return $this->userFlairType;
    }

    /**
     * Set the currently authenticated user's flair type for this subreddit,
     * if any; e.g. "text" or "richtext"
     *
     * @param string $userFlairType
     * @return $this
     */
    protected function setUserFlairType(string $userFlairType) {
        $this->userFlairType = $userFlairType;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has added this
     * subreddit to their favorites list from its "community details" sidebar
     * widget
     *
     * @return bool
     */
    public function getUserHasFavorited(): bool {
        return $this->userHasFavorited;
    }

    /**
     * Set whether or not the currently authenticated user has added this
     * subreddit to their favorites list from its "community details" sidebar
     * widget
     *
     * @param bool $userHasFavorited
     * @return $this
     */
    protected function setUserHasFavorited(bool $userHasFavorited) {
        $this->userHasFavorited = $userHasFavorited;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user is banned from
     * participating in this subreddit
     *
     * @return bool
     */
    public function getUserIsBanned(): bool {
        return $this->userIsBanned;
    }

    /**
     * Set whether or not the currently authenticated user is banned from
     * participating in this subreddit
     *
     * @param bool $userIsBanned
     * @return $this
     */
    protected function setUserIsBanned(bool $userIsBanned) {
        $this->userIsBanned = $userIsBanned;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has been added as an
     * approved user in this subreddit
     *
     * @return bool
     */
    public function getUserIsContributor(): bool {
        return $this->userIsContributor;
    }

    /**
     * Set whether or not the currently authenticated user has been added as an
     * approved user in this subreddit
     *
     * @param bool $userIsContributor
     * @return $this
     */
    protected function setUserIsContributor(bool $userIsContributor) {
        $this->userIsContributor = $userIsContributor;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has been added as a
     * moderator in this subreddit
     *
     * @return bool
     */
    public function getUserIsModerator(): bool {
        return $this->userIsModerator;
    }

    /**
     * Set whether or not the currently authenticated user has been added as a
     * moderator in this subreddit
     *
     * @param bool $userIsModerator
     * @return $this
     */
    protected function setUserIsModerator(bool $userIsModerator) {
        $this->userIsModerator = $userIsModerator;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has been muted in this
     * subreddit
     *
     * @return bool
     */
    public function getUserIsMuted(): bool {
        return $this->userIsMuted;
    }

    /**
     * Set whether or not the currently authenticated user has been muted in this
     * subreddit
     *
     * @param bool $userIsMuted
     * @return $this
     */
    protected function setUserIsMuted(bool $userIsMuted) {
        $this->userIsMuted = $userIsMuted;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has subscribed to this
     * subreddit
     *
     * @return bool
     */
    public function getUserIsSubscriber(): bool {
        return $this->userIsSubscriber;
    }

    /**
     * Set whether or not the currently authenticated user has subscribed to this
     * subreddit
     *
     * @param bool $userIsSubscriber
     * @return $this
     */
    protected function setUserIsSubscriber(bool $userIsSubscriber) {
        $this->userIsSubscriber = $userIsSubscriber;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has enabled their
     * user flair to be displayed on this subreddit
     *
     * @return bool|null
     */
    public function getUserSrFlairEnabled(): ?bool {
        return $this->userSrFlairEnabled;
    }

    /**
     * Set whether or not the currently authenticated user has enabled their
     * user flair to be displayed on this subreddit
     *
     * @param bool $userSrFlairEnabled
     * @return $this
     */
    protected function setUserSrFlairEnabled(bool $userSrFlairEnabled = null) {
        $this->userSrFlairEnabled = $userSrFlairEnabled;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has enabled this
     * subreddit's custom CSS ("community theme").
     *
     * @return bool
     */
    public function getUserSrThemeEnabled(): bool {
        return $this->userSrThemeEnabled;
    }

    /**
     * Set whether or not the currently authenticated user has enabled this
     * subreddit's custom CSS ("community theme")
     *
     * @param bool $userSrThemeEnabled
     * @return $this
     */
    protected function setUserSrThemeEnabled(bool $userSrThemeEnabled) {
        $this->userSrThemeEnabled = $userSrThemeEnabled;
        return $this;
    }

    /**
     * Get the number of video streaming links in this subreddit (?) This seems
     * to max out at 100.
     *
     * @return int|null
     * @todo verify what exactly this corresponds to
     */
    public function getVideostreamLinksCount(): ?int {
        return $this->videostreamLinksCount;
    }

    /**
     * Set the number of video streaming links in this subreddit (?) This seems
     * to max out at 100.
     *
     * @param int $videostreamLinksCount
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setVideostreamLinksCount(int $videostreamLinksCount = null) {
        $this->videostreamLinksCount = $videostreamLinksCount;
        return $this;
    }

    /**
     * Get the advertising whitelist status for this subreddit, if set. This
     * indicates which types of ads are eligible to be displayed.
     *
     * @return string|null
     */
    public function getWhitelistStatus(): ?string {
        return $this->whitelistStatus;
    }

    /**
     * Set the advertising whitelist status for this subreddit, if set. This
     * indicates which types of ads are eligible to be displayed.
     *
     * @param string $whitelistStatus
     * @return $this
     */
    protected function setWhitelistStatus(string $whitelistStatus = null) {
        $this->whitelistStatus = $whitelistStatus;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has access to edit
     * some or all of this subreddit's wiki.
     *
     * @return bool|null
     */
    public function getWikiEnabled(): ?bool {
        return $this->wikiEnabled;
    }

    /**
     * Set whether or not the currently authenticated user has access to edit
     * some or all of this subreddit's wiki.
     *
     * @param bool $wikiEnabled
     * @return $this
     */
    protected function setWikiEnabled(bool $wikiEnabled = null) {
        $this->wikiEnabled = $wikiEnabled;
        return $this;
    }

    /**
     * Get the numeric whitelist status key, if any. A text version of the
     * corresponding whitelist status is available in getWhitelistStatus()
     *
     * @return int|null
     * @see getWhitelistStatus()
     */
    public function getWls(): ?int {
        return $this->wls;
    }

    /**
     * Set the numeric whitelist status key, if any
     *
     * @param int $wls
     * @return $this
     * @see setWhitelistStatus()
     */
    protected function setWls(int $wls = null) {
        $this->wls = $wls;
        return $this;
    }

}
