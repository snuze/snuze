<?php

declare(strict_types=1);

namespace snuze\Reddit\Thing;

/**
 * The Link class represents the significant properties of a link, otherwise
 * known as a submission, post, or thread. An attempt has been made to map all
 * fields supplied by the API, regardless of their utility (or lack thereof).
 *
 * Implementation warning: Array properties are currently populated as-is,
 * without any further processing. Calling their getters will return an array
 * holding key/value pairs defining the property. This is subject to change in a
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
class Link extends Thing
{
    /*
     * These properties are documented alongside their accessors, below.
     */

    protected $allAwardings = [];
    protected $allowLiveComments = true;
    protected $approved = null; //bool
    protected $approvedAtUtc = null;
    protected $approvedBy = null;
    protected $archived = false;
    protected $author = '';
    protected $authorCakeday = null; //bool if set
    protected $authorFlairBackgroundColor = null;
    protected $authorFlairCssClass = null;
    protected $authorFlairRichtext = [];
    protected $authorFlairTemplateId = null;
    protected $authorFlairText = null;
    protected $authorFlairTextColor = null;
    protected $authorFlairType = '';
    protected $authorFullname = '';
    protected $authorPatreonFlair = false;
    protected $bannedAtUtc = null; //float if set
    protected $bannedBy = null;
    protected $canGild = true;
    protected $canModPost = false;
    protected $category = null;
    protected $clicked = false;
    protected $collections = null; //array if set; see json-samples/link-with-collection.json
    protected $contentCategories = null; //array if set
    protected $contestMode = false;
    protected $crosspostParent = null; //string if set
    protected $crosspostParentList = null; //array if set
    protected $discussionType = null;
    protected $distinguished = '';
    protected $domain = '';
    protected $downs = 0;
    protected $edited = false; //float
    protected $eventEnd = null; //float
    protected $eventIsLive = null; //bool
    protected $eventStart = null; //float
    protected $gilded = 0;
    protected $gildings = [];
    protected $hidden = false;
    protected $hideScore = false;
    protected $ignoreReports = null;
    protected $isCrosspostable = true;
    protected $isMeta = false;
    protected $isOriginalContent = false;
    protected $isRedditMediaDomain = false;
    protected $isRobotIndexable = true;
    protected $isSelf = false;
    protected $isVideo = false;
    protected $likes = null;
    protected $linkFlairBackgroundColor = '';
    protected $linkFlairCssClass = null;
    protected $linkFlairRichtext = [];
    protected $linkFlairTemplateId = null; //string if set
    protected $linkFlairText = '';
    protected $linkFlairTextColor = '';
    protected $linkFlairType = '';
    protected $locked = false;
    protected $media = null; //array if set
    protected $mediaEmbed = [];
    protected $mediaMetadata = null; //array if set
    protected $mediaOnly = false;
    protected $modNote = null;
    protected $modReasonBy = null;
    protected $modReasonTitle = null;
    protected $modReports = [];
    protected $name = '';
    protected $noFollow = false;
    protected $numComments = 0;
    protected $numCrossposts = 0;
    protected $numReports = null;
    protected $over18 = false;
    protected $parentWhitelistStatus = ''; //advertising whitelist
    protected $permalink = '';
    protected $pinned = false;
    protected $postHint = '';
    protected $preview = [];
    protected $pwls = 0;
    protected $quarantine = false;
    protected $removalReason = null;
    protected $removed = null; //bool
    protected $reportReasons = null;
    protected $rteMode = null; //string
    protected $saved = false;
    protected $score = 0;
    protected $secureMedia = null;
    protected $secureMediaEmbed = [];
    protected $selftext = '';
    protected $selftextHtml = '';
    protected $sendReplies = false;
    protected $spam = null;
    protected $spoiler = false;
    protected $stewardReports = [];
    protected $stickied = false;
    protected $subreddit = '';
    protected $subredditId = '';
    protected $subredditNamePrefixed = '';
    protected $subredditSubscribers = 0;
    protected $subredditType = '';
    protected $suggestedSort = null;
    protected $thumbnail = '';
    protected $thumbnailHeight = null;
    protected $thumbnailWidth = null;
    protected $title = '';
    protected $totalAwardsReceived = 0;
    protected $ups = 0;
    protected $url = '';
    protected $userReports = [];
    protected $viewCount = null; //int
    protected $visited = false;
    protected $whitelistStatus = ''; //advertising whitelist
    protected $wls = 0;

    /**
     * Constructor.
     */
    public function __construct() {
        /* All Thing children must call parent ctor and set property names */
        parent::__construct(Thing::KIND_LINK);
        $this->_propertyNames = array_keys(get_object_vars($this));
        $this->debug('ctor args: ' . var_export(func_get_args(), true));
    }

    /**
     * Get an array defining the type and quantity of all awards (gold, silver,
     * platinum...) this link has received.
     *
     * @return array
     */
    public function getAllAwardings(): array {
        return $this->allAwardings;
    }

    /**
     * Set the array defining the type and quantity of all awards (gold, silver,
     * platinum...) this link has received.
     *
     * @param array $allAwardings
     * @return $this
     */
    protected function setAllAwardings(array $allAwardings) {
        $this->allAwardings = $allAwardings;
        return $this;
    }

    /**
     * Get whether or not comments on this link can be sorted by "live," which
     * is an experimental feature.
     *
     * @return bool
     */
    public function getAllowLiveComments(): bool {
        return $this->allowLiveComments;
    }

    /**
     * Set whether or not comments on this link can be sorted by "live," which
     * is an experimental feature.
     *
     * @param bool $allowLiveComments
     * @return $this
     */
    protected function setAllowLiveComments(bool $allowLiveComments) {
        $this->allowLiveComments = $allowLiveComments;
        return $this;
    }

    /**
     * Get whether or not this link was manually approved by a moderator.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return bool|null
     */
    public function getApproved(): ?bool {
        return $this->approved;
    }

    /**
     * Set whether or not this link was manually approved by a moderator.
     *
     * @param bool $approved
     * @return $this
     */
    public function setApproved(bool $approved = null) {
        $this->approved = $approved;
        return $this;
    }

    /**
     * If this link was manually approved by a moderator, get the unix epoch at
     * which that occurred, else null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return float|null
     */
    public function getApprovedAtUtc(): ?float {
        return $this->approvedAtUtc;
    }

    /**
     * Set the epoch timestamp at which this link was manually approved by a
     * moderator, if that occurred.
     *
     * @param float $approvedAtUtc
     * @return $this
     */
    protected function setApprovedAtUtc(float $approvedAtUtc = null) {
        $this->approvedAtUtc = $approvedAtUtc;
        return $this;
    }

    /**
     * If this link was approved by a moderator, get the username of the mod
     * who approved it, else null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     */
    public function getApprovedBy(): ?string {
        return $this->approvedBy;
    }

    /**
     * Set the username of the moderator who manually approved this link, or
     * null if it wasn't.
     *
     * @param string $approvedBy
     * @return $this
     */
    protected function setApprovedBy(string $approvedBy = null) {
        $this->approvedBy = $approvedBy;
        return $this;
    }

    /**
     * Get whether or not this link is archived and can no longer be voted or
     * commented on.
     *
     * @return bool
     */
    public function getArchived(): bool {
        return $this->archived;
    }

    /**
     * Set whether or not this link is archived and can no longer be voted or
     * commented on.
     *
     * @param bool $archived
     * @return $this
     */
    protected function setArchived(bool $archived) {
        $this->archived = $archived;
        return $this;
    }

    /**
     * Get the username of the Reddit account that submitted this link.
     *
     * @return string
     */
    public function getAuthor(): string {
        return $this->author;
    }

    /**
     * Set the username of the Reddit account that submitted this link.
     *
     * @param string $author
     * @return $this
     */
    protected function setAuthor(string $author) {
        $this->author = $author;
        return $this;
    }

    /**
     * Get whether or not the "cake day" flair should decorate this link to
     * celebrate its author's Reddit birthday.
     *
     * This property is transient and will only be true if the link is fetched
     * during the duration of its author's cake day.
     *
     * @return bool|null
     */
    public function getAuthorCakeday(): ?bool {
        return $this->authorCakeday;
    }

    /**
     * Set whether or not the "cake day" flair should decorate this link to
     * celebrate its author's Reddit birthday.
     *
     * @param bool $authorCakeday
     * @return $this
     */
    protected function setAuthorCakeday(bool $authorCakeday = null) {
        $this->authorCakeday = $authorCakeday;
        return $this;
    }

    /**
     * Get the HTML hex color code for the author's user flair background, if
     * any. The literal string "transparent" has also been observed in this
     * property.
     *
     * @return string|null
     */
    public function getAuthorFlairBackgroundColor(): ?string {
        return $this->authorFlairBackgroundColor;
    }

    /**
     * Set the HTML hex color code for the author's user flair background, if any
     *
     * @param string $authorFlairBackgroundColor
     * @return $this
     */
    protected function setAuthorFlairBackgroundColor(string $authorFlairBackgroundColor
            = null) {
        $this->authorFlairBackgroundColor = $authorFlairBackgroundColor;
        return $this;
    }

    /**
     * Get the CSS class corresponding to the author's user flair, if any
     *
     * @return string|null
     */
    public function getAuthorFlairCssClass(): ?string {
        return $this->authorFlairCssClass;
    }

    /**
     * Set the CSS class corresponding to the author's user flair, if any
     *
     * @param string $authorFlairCssClass
     * @return $this
     */
    protected function setAuthorFlairCssClass(string $authorFlairCssClass = null) {
        $this->authorFlairCssClass = $authorFlairCssClass;
        return $this;
    }

    /**
     * Get the array containing the elements that define the author's user flair,
     * if any.
     *
     * @return array
     */
    public function getAuthorFlairRichtext(): array {
        return $this->authorFlairRichtext;
    }

    /**
     * Set the array containing the elements that define the author's user flair,
     * if any.
     *
     * @param array $authorFlairRichtext
     * @return $this
     */
    protected function setAuthorFlairRichtext(array $authorFlairRichtext) {
        $this->authorFlairRichtext = $authorFlairRichtext;
        return $this;
    }

    /**
     * Get the 36-character UUID of the author's user flair template, if any
     *
     * @return string|null
     */
    public function getAuthorFlairTemplateId(): ?string {
        return $this->authorFlairTemplateId;
    }

    /**
     * Set the 36-character UUID of the author's user flair template, if any
     *
     * @param string $authorFlairTemplateId
     * @return $this
     */
    protected function setAuthorFlairTemplateId(string $authorFlairTemplateId = null) {
        $this->authorFlairTemplateId = $authorFlairTemplateId;
        return $this;
    }

    /**
     * Get the author's user flair text, if any
     *
     * @return string|null
     */
    public function getAuthorFlairText(): ?string {
        return $this->authorFlairText;
    }

    /**
     * Set the author's user flair text, if any
     *
     * @param string $authorFlairText
     * @return $this
     */
    protected function setAuthorFlairText(string $authorFlairText = null) {
        $this->authorFlairText = $authorFlairText;
        return $this;
    }

    /**
     * Get the color, either "dark" or "light", of the author's user flair text,
     * if any
     *
     * @return string|null
     */
    public function getAuthorFlairTextColor(): ?string {
        return $this->authorFlairTextColor;
    }

    /**
     * Set the color, either "dark" or "light", of the author's user flair text,
     * if any
     *
     * @param string $authorFlairTextColor
     * @return $this
     */
    protected function setAuthorFlairTextColor(string $authorFlairTextColor = null) {
        $this->authorFlairTextColor = $authorFlairTextColor;
        return $this;
    }

    /**
     * Get the author's user flair type, if any; e.g. "text" or "richtext"
     *
     * @return string
     */
    public function getAuthorFlairType(): string {
        return $this->authorFlairType;
    }

    /**
     * Set the author's user flair type, if any; e.g. "text" or "richtext"
     *
     * @param string $authorFlairType
     * @return $this
     */
    protected function setAuthorFlairType(string $authorFlairType) {
        $this->authorFlairType = $authorFlairType;
        return $this;
    }

    /**
     * Get the Reddit internal "thing" fullname of the author, e.g. "t2_bva6"
     *
     * @return string
     */
    public function getAuthorFullname(): string {
        return $this->authorFullname;
    }

    /**
     * Set the Reddit internal "thing" fullname of the author, e.g. "t2_bva6"
     *
     * @param string $authorFullname
     * @return $this
     */
    protected function setAuthorFullname(string $authorFullname) {
        $this->authorFullname = $authorFullname;
        return $this;
    }

    /**
     * Get whether or not the author is a patron (via Patreon) of the subreddit
     * where this link is posted, granting them special flair
     *
     * @return bool
     */
    public function getAuthorPatreonFlair(): bool {
        return $this->authorPatreonFlair;
    }

    /**
     * Set whether or not the author is a patron (via Patreon) of the subreddit
     * where this link is posted, granting them special flair
     *
     * @param bool $authorPatreonFlair
     * @return $this
     */
    protected function setAuthorPatreonFlair(bool $authorPatreonFlair) {
        $this->authorPatreonFlair = $authorPatreonFlair;
        return $this;
    }

    /**
     * If this link was removed by a moderator, get the unix epoch at which
     * this occurred, else null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return int|null
     */
    public function getBannedAtUtc(): ?int {
        return $this->bannedAtUtc;
    }

    /**
     * Set the unix epoch at which this post was removed by a moderator, if
     * that occurred.
     *
     * @param int $bannedAtUtc
     * @return $this
     */
    protected function setBannedAtUtc(int $bannedAtUtc = null) {
        $this->bannedAtUtc = $bannedAtUtc;
        return $this;
    }

    /**
     * If this link was removed by a moderator, get the username of the mod
     * who removed it. If this link was removed by the system (e.g. flagged
     * as spam) but that decision hasn't been confirmed by a moderator, returns
     * the literal string '[auto]'. If the link has not been removed either way,
     * returns null.
     *
     * This is a mutant field. When a link is removed by a human moderator,
     * Reddit sends that mod's username. When a link is removed by the system,
     * e.g. flagged as spam, Reddit sends boolean true. I had to settle on one
     * datatype, so I went with a string and picked the '[auto]' value to
     * distinguish unconfirmed automated removals.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     */
    public function getBannedBy(): ?string {
        if ($this->bannedBy === true) {
            return '[auto]';
        }
        return $this->bannedBy;
    }

    /**
     * Set the username of the moderator who removed this link, if that occurred,
     * or the literal string '[auto]' if the link was removed by the system
     *
     * @param mixed $bannedBy
     * @return $this
     */
    protected function setBannedBy($bannedBy = null) {
        if ($bannedBy === true) {
            /* Automatic system removal e.g. spam */
            $bannedBy = '[auto]';
        }
        $this->bannedBy = $bannedBy;
        return $this;
    }

    /**
     * Get whether or not the link is eligible for gilding by the currently
     * authenticated user. (You can't gild your own links.)
     *
     * @return bool
     */
    public function getCanGild(): bool {
        return $this->canGild;
    }

    /**
     * Set whether or not the link is eligible for gilding by the currently
     * authenticated user. (You can't gild your own links.)
     *
     * @param bool $canGild
     * @return $this
     */
    protected function setCanGild(bool $canGild) {
        $this->canGild = $canGild;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user is permitted to
     * moderate this link.
     *
     * @return bool
     */
    public function getCanModPost(): bool {
        return $this->canModPost;
    }

    /**
     * Set whether or not the currently authenticated user is permitted to
     * moderate this link.
     *
     * @param bool $canModPost
     * @return $this
     */
    protected function setCanModPost(bool $canModPost) {
        $this->canModPost = $canModPost;
        return $this;
    }

    /**
     * Get the category name, if any, assigned to this link.
     *
     * @return string|null
     */
    public function getCategory(): ?string {
        return $this->category;
    }

    /**
     * Set the category name, if any, assigned to this link.
     *
     * @param string $category
     * @return $this
     */
    protected function setCategory(string $category = null) {
        $this->category = $category;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has opened this
     * link, if the user has the "Remember what links you've visited across
     * computers" preference enabled (?) Not entirely sure on this.
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getClicked(): bool {
        return $this->clicked;
    }

    /**
     * Set whether or not the currently authenticated user has opened this
     * link, if the user has the "Remember what links you've visited across
     * computers" preference enabled (?) Not entirely sure on this.
     *
     * @param bool $clicked
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setClicked(bool $clicked) {
        $this->clicked = $clicked;
        return $this;
    }

    /**
     * If this link is a collection, get the array that defines the other links
     * in the collection, else null
     *
     * @return array|null
     */
    public function getCollections(): ?array {
        return $this->collections;
    }

    /**
     * If this link is a collection, set the array that defines the other links
     * in the collection
     *
     * @param array $collections
     * @return $this
     */
    protected function setCollections(array $collections = null) {
        $this->collections = $collections;
        return $this;
    }

    /**
     * Get an array that defines this link's content categories, if any are
     * assigned
     *
     * @return array|null
     */
    public function getContentCategories(): ?array {
        return $this->contentCategories;
    }

    /**
     * Set an array that defines this link's content categories, if any are
     * assigned
     *
     * @param array $contentCategories
     * @return $this
     */
    protected function setContentCategories(array $contentCategories = null) {
        $this->contentCategories = $contentCategories;
        return $this;
    }

    /**
     * Get whether or not this link has been designated as being in contest mode
     * for purposes of comment display
     *
     * @return bool
     */
    public function getContestMode(): bool {
        return $this->contestMode;
    }

    /**
     * Set whether or not this link has been designated as being in contest mode
     * for purposes of comment display
     *
     * @param bool $contestMode
     * @return $this
     */
    protected function setContestMode(bool $contestMode) {
        $this->contestMode = $contestMode;
        return $this;
    }

    /**
     * If this link is a crosspost, get the Reddit internal fullname of the
     * original link e.g. "t3_clcb1h", else null
     *
     * @return string|null
     */
    public function getCrosspostParent(): ?string {
        return $this->crosspostParent;
    }

    /**
     * If this link is a crosspost, set the Reddit internal fullname of the
     * original link
     *
     * @param string $crosspostParent
     * @return $this
     */
    protected function setCrosspostParent(string $crosspostParent = null) {
        $this->crosspostParent = $crosspostParent;
        return $this;
    }

    /**
     * If this link is a crosspost, get an array defining the significant
     * properties of the original link, else null
     *
     * @return array|null
     */
    public function getCrosspostParentList(): ?array {
        return $this->crosspostParentList;
    }

    /**
     * If this link is a crosspost, set an array defining the significant
     * properties of the original link
     *
     * @param array $crosspostParentList
     * @return $this
     */
    protected function setCrosspostParentList(array $crosspostParentList = null) {
        $this->crosspostParentList = $crosspostParentList;
        return $this;
    }

    /**
     * Get the discussion type for this link's comment thread, if any. If
     * the link was created as a chat post (e.g. "t3_cmhugu"), this will be
     * set to "CHAT"
     *
     * @return string|null
     */
    public function getDiscussionType(): ?string {
        return $this->discussionType;
    }

    /**
     * Set the discussion type for this link's comment thread, if any
     *
     * @param string $discussionType
     * @return $this
     */
    protected function setDiscussionType(string $discussionType = null) {
        $this->discussionType = $discussionType;
        return $this;
    }

    /**
     * Get the type of distinguishment set on this link (e.g. 'moderator',
     * 'admin', 'special'), if any
     *
     * @return string
     */
    public function getDistinguished(): string {
        return $this->distinguished;
    }

    /**
     * Set the type of distinguishment set on this link (e.g. 'moderator',
     * 'admin', 'special'), if any
     *
     * @param string $distinguished
     * @return $this
     */
    protected function setDistinguished(string $distinguished) {
        $this->distinguished = $distinguished;
        return $this;
    }

    /**
     * Get the domain this link points to. For text-only self posts, this
     * will be the string 'self.' followed by the subreddit name.
     *
     * @return string
     */
    public function getDomain(): string {
        return $this->domain;
    }

    /**
     * Set the domain this link points to. For text-only self posts, this
     * will be the string 'self.' followed by the subreddit name.
     *
     * @param string $domain
     * @return $this
     */
    protected function setDomain(string $domain) {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Get the number of downvotes that have been recorded on this link. This
     * is generally not provided by the API; instead, refer to "ups" and "score"
     *
     * @return int
     * @see getScore()
     * @see getUps()
     */
    public function getDowns(): int {
        return $this->downs;
    }

    /**
     * Set the number of downvotes that have been recorded on this link
     *
     * @param int $downs
     * @return $this
     */
    protected function setDowns(int $downs) {
        $this->downs = $downs;
        return $this;
    }

    /**
     * Get the unix epoch timestamp at which this link was edited, or 0.0 if it
     * hasn't been edited.
     *
     * This is a mutant field. If a link has been edited, Reddit returns the
     * epoch timestamp as a float. If it hasn't been edited, Reddit returns
     * boolean false. I had to settle on one datatype, so float it is.
     *
     * @return float
     */
    public function getEdited(): float {
        if ($this->edited === false) {
            return 0.0;
        }
        return $this->edited;
    }

    /**
     * Set the unix epoch timestamp at which this link was edited, or 0.0 if it
     * hasn't been edited.
     *
     * This is a mutant field. If a link has been edited, Reddit returns the
     * epoch timestamp as a float. If it hasn't been edited, Reddit returns
     * boolean false. I had to settle on one datatype, so float it is.
     *
     * @param type $edited
     * @return $this
     */
    protected function setEdited($edited) {
        $this->edited = (float) $edited;
        return $this;
    }

    /**
     * If this link is marked as an event, get the unix epoch at which the
     * event is scheduled to end, else null
     *
     * @return float|null
     */
    public function getEventEnd(): ?float {
        return $this->eventEnd;
    }

    /**
     * If this link is marked as an event, set the unix epoch at which the
     * event is scheduled to end, else null
     *
     * @param float $eventEnd
     * @return $this
     */
    public function setEventEnd(float $eventEnd = null) {
        $this->eventEnd = $eventEnd;
        return $this;
    }

    /**
     * If this link is marked as an event, get whether or not it's currently
     * active at the time the link was fetched
     *
     * @return bool|null
     */
    public function getEventIsLive(): ?bool {
        return $this->eventIsLive;
    }

    /**
     * If this link is marked as an event, set whether or not it's currently
     * active at the time the link was fetched
     *
     * @param bool $eventIsLive
     * @return $this
     */
    public function setEventIsLive(bool $eventIsLive = null) {
        $this->eventIsLive = $eventIsLive;
        return $this;
    }

    /**
     * If this link is marked as an event, get the unix epoch at which the
     * event is scheduled to begin, else null
     *
     * @return float|null
     */
    public function getEventStart(): ?float {
        return $this->eventStart;
    }

    /**
     * If this link is marked as an event, set the unix epoch at which the
     * event is scheduled to begin, else null
     *
     * @param float $eventStart
     * @return $this
     */
    public function setEventStart(float $eventStart = null) {
        $this->eventStart = $eventStart;
        return $this;
    }

    /**
     * Get the number of times this link has been gilded
     *
     * @return int
     */
    public function getGilded(): int {
        return $this->gilded;
    }

    /**
     * Set the number of times this link has been gilded
     *
     * @param int $gilded
     * @return $this
     */
    protected function setGilded(int $gilded) {
        $this->gilded = $gilded;
        return $this;
    }

    /**
     * Get an array that defines the gildings for this link
     *
     * @return array
     */
    public function getGildings(): array {
        return $this->gildings;
    }

    /**
     * Set an array that defines the gildings for this link
     *
     * @param array $gildings
     * @return $this
     */
    protected function setGildings(array $gildings) {
        $this->gildings = $gildings;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has hidden this link
     *
     * @return bool
     */
    public function getHidden(): bool {
        return $this->hidden;
    }

    /**
     * Set whether or not the currently authenticated user has hidden this link
     *
     * @param bool $hidden
     * @return $this
     */
    protected function setHidden(bool $hidden) {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Get whether or not the vote score of this link was hidden at the time the
     * link was fetched.
     *
     * @return bool
     */
    public function getHideScore(): bool {
        return $this->hideScore;
    }

    /**
     * Set whether or not the vote score of this link was hidden at the time the
     * link was fetched.
     *
     * @param bool $hideScore
     * @return $this
     */
    protected function setHideScore(bool $hideScore) {
        $this->hideScore = $hideScore;
        return $this;
    }

    /**
     * Get whether or not a moderator has set the "ignore reports" flag on this
     * link.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return bool|null
     */
    public function getIgnoreReports(): ?bool {
        return $this->ignoreReports;
    }

    /**
     * Set whether or not a moderator has set the "ignore reports" flag on this
     * link.
     *
     * @param bool $ignoreReports
     * @return $this
     */
    public function setIgnoreReports(bool $ignoreReports = null) {
        $this->ignoreReports = $ignoreReports;
        return $this;
    }

    /**
     * Get whether or not this link is eligible to be crossposted to another
     * subreddit.
     *
     * @return bool
     */
    public function getIsCrosspostable(): bool {
        return $this->isCrosspostable;
    }

    /**
     * Set whether or not this link is eligible to be crossposted to another
     * subreddit.
     *
     * @param bool $isCrosspostable
     * @return $this
     */
    protected function setIsCrosspostable(bool $isCrosspostable) {
        $this->isCrosspostable = $isCrosspostable;
        return $this;
    }

    /**
     * Get whether or not this link is marked as a meta-post (?)
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getIsMeta(): bool {
        return $this->isMeta;
    }

    /**
     * Set whether or not this link is marked as a meta-post (?)
     *
     * @param bool $isMeta
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setIsMeta(bool $isMeta) {
        $this->isMeta = $isMeta;
        return $this;
    }

    /**
     * Get whether or not this link is marked with the "OC" tag
     *
     * @return bool
     */
    public function getIsOriginalContent(): bool {
        return $this->isOriginalContent;
    }

    /**
     * Set whether or not this link is marked with the "OC" tag
     *
     * @param bool $isOriginalContent
     * @return $this
     */
    protected function setIsOriginalContent(bool $isOriginalContent) {
        $this->isOriginalContent = $isOriginalContent;
        return $this;
    }

    /**
     * Get whether or not the link target points to a Reddit-owned domain
     *
     * @return bool
     */
    public function getIsRedditMediaDomain(): bool {
        return $this->isRedditMediaDomain;
    }

    /**
     * Set whether or not the link target points to a Reddit-owned domain
     *
     * @param bool $isRedditMediaDomain
     * @return $this
     */
    protected function setIsRedditMediaDomain(bool $isRedditMediaDomain) {
        $this->isRedditMediaDomain = $isRedditMediaDomain;
        return $this;
    }

    /**
     * Get whether or not this link is eligible to be indexed by search engines.
     * When this is false, the link's page will have a <meta name="robots"
     * content="noindex,nofollow" /> tag added to it.
     *
     * @return bool
     */
    public function getIsRobotIndexable(): bool {
        return $this->isRobotIndexable;
    }

    /**
     * Set whether or not this link is eligible to be indexed by search engines.
     *
     * @param bool $isRobotIndexable
     * @return $this
     */
    protected function setIsRobotIndexable(bool $isRobotIndexable) {
        $this->isRobotIndexable = $isRobotIndexable;
        return $this;
    }

    /**
     * Get whether or not this link is a self (text-only) post.
     *
     * @return bool
     */
    public function getIsSelf(): bool {
        return $this->isSelf;
    }

    /**
     * Set whether or not this link is a self (text-only) post.
     *
     * @param bool $isSelf
     * @return $this
     */
    protected function setIsSelf(bool $isSelf) {
        $this->isSelf = $isSelf;
        return $this;
    }

    /**
     * Get whether or not this link is a video that was uploaded directly to
     * Reddit and is hosted at v.redd.it
     *
     * @return bool
     */
    public function getIsVideo(): bool {
        return $this->isVideo;
    }

    /**
     * Set whether or not this link is a video that was uploaded directly to
     * Reddit and is hosted at v.redd.it
     *
     * @param bool $isVideo
     * @return $this
     */
    protected function setIsVideo(bool $isVideo) {
        $this->isVideo = $isVideo;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has voted on this
     * link. A value of true indicates an upvote, a value of false indicates
     * a downvote, a null value indicates no vote.
     *
     * @return bool|null
     */
    public function getLikes(): ?bool {
        return $this->likes;
    }

    /**
     * Set whether or not the currently authenticated user has voted on this
     * link. A value of true indicates an upvote, a value of false indicates
     * a downvote, a null value indicates no vote.
     *
     * @param bool $likes
     * @return $this
     */
    protected function setLikes(bool $likes = null) {
        $this->likes = $likes;
        return $this;
    }

    /**
     * Get the HTML hex color code for the link's flair background, if any
     *
     * @return string
     */
    public function getLinkFlairBackgroundColor(): string {
        return $this->linkFlairBackgroundColor;
    }

    /**
     * Set the HTML hex color code for the link's flair background, if any
     *
     * @param string $linkFlairBackgroundColor
     * @return $this
     */
    protected function setLinkFlairBackgroundColor(string $linkFlairBackgroundColor) {
        $this->linkFlairBackgroundColor = $linkFlairBackgroundColor;
        return $this;
    }

    /**
     * Get the CSS class corresponding to the link's flair, if any
     *
     * @return string|null
     */
    public function getLinkFlairCssClass(): ?string {
        return $this->linkFlairCssClass;
    }

    /**
     * Set the CSS class corresponding to the link's flair, if any
     *
     * @param string $linkFlairCssClass
     * @return $this
     */
    protected function setLinkFlairCssClass(string $linkFlairCssClass = null) {
        $this->linkFlairCssClass = $linkFlairCssClass;
        return $this;
    }

    /**
     * Get the array containing the elements that define the link's flair, if any
     *
     * @return array
     */
    public function getLinkFlairRichtext(): array {
        return $this->linkFlairRichtext;
    }

    /**
     * Set the array containing the elements that define the link's flair, if any
     *
     * @param array $linkFlairRichtext
     * @return $this
     */
    protected function setLinkFlairRichtext(array $linkFlairRichtext) {
        $this->linkFlairRichtext = $linkFlairRichtext;
        return $this;
    }

    /**
     * Get the 36-character UUID of the link's flair template, if any
     *
     * @return string|null
     */
    public function getLinkFlairTemplateId(): ?string {
        return $this->linkFlairTemplateId;
    }

    /**
     * Set the 36-character UUID of the link's flair template, if any
     *
     * @param string $linkFlairTemplateId
     * @return $this
     */
    protected function setLinkFlairTemplateId(string $linkFlairTemplateId = null) {
        $this->linkFlairTemplateId = $linkFlairTemplateId;
        return $this;
    }

    /**
     * Get the link's flair text, if any
     *
     * @return string
     */
    public function getLinkFlairText(): string {
        return $this->linkFlairText;
    }

    /**
     * Set the link's flair text, if any
     *
     * @param string $linkFlairText
     * @return $this
     */
    protected function setLinkFlairText(string $linkFlairText) {
        $this->linkFlairText = $linkFlairText;
        return $this;
    }

    /**
     * Get the color, either "dark" or "light", of the link's flair text, if any
     *
     * @return string
     */
    public function getLinkFlairTextColor(): string {
        return $this->linkFlairTextColor;
    }

    /**
     * Set the color, either "dark" or "light", of the link's flair text, if any
     *
     * @param string $linkFlairTextColor
     * @return $this
     */
    protected function setLinkFlairTextColor(string $linkFlairTextColor) {
        $this->linkFlairTextColor = $linkFlairTextColor;
        return $this;
    }

    /**
     * Get the link's flair type, if any; e.g. "text" or "richtext"
     *
     * @return string
     */
    public function getLinkFlairType(): string {
        return $this->linkFlairType;
    }

    /**
     * Set the link's flair type, if any; e.g. "text" or "richtext"
     *
     * @param string $linkFlairType
     * @return $this
     */
    protected function setLinkFlairType(string $linkFlairType) {
        $this->linkFlairType = $linkFlairType;
        return $this;
    }

    /**
     * Get whether or not this link has been locked, preventing further
     * comments from being posted
     *
     * @return bool
     */
    public function getLocked(): bool {
        return $this->locked;
    }

    /**
     * Get whether or not this link has been locked, preventing further
     * comments from being posted
     *
     * @param bool $locked
     * @return $this
     */
    protected function setLocked(bool $locked) {
        $this->locked = $locked;
        return $this;
    }

    /**
     * Get an array defining any recognized media this link points to. Reddit
     * is able to identify media types and metadata from itself, as well as
     * from certain external sites like YouTube, Twitter, etc.
     *
     * @return array|null
     */
    public function getMedia(): ?array {
        return $this->media;
    }

    /**
     * Set an array defining any recognized media this link points to. Reddit
     * is able to identify media types and metadata from itself, as well as
     * from certain external sites like YouTube, Twitter, etc.
     *
     * @param array $media
     * @return $this
     */
    protected function setMedia(array $media = null) {
        $this->media = $media;
        return $this;
    }

    /**
     * Get an array defining the HTML properties required to embed recognized
     * media objects
     *
     * @return array
     * @see getMedia()
     */
    public function getMediaEmbed(): array {
        return $this->mediaEmbed;
    }

    /**
     * Set an array defining the HTML properties required to embed recognized
     * media objects
     *
     * @param array $mediaEmbed
     * @return $this
     * @see setMedia()
     */
    protected function setMediaEmbed(array $mediaEmbed) {
        $this->mediaEmbed = $mediaEmbed;
        return $this;
    }

    /**
     * Get an array defining any recognized media that was linked to within the
     * text (self post) portion of this link. Reddit is able to identify media
     * types and metadata from itself, as well as from certain external sites
     * like YouTube, Twitter, etc.
     *
     * @return array|null
     */
    public function getMediaMetadata(): ?array {
        return $this->mediaMetadata;
    }

    /**
     * Set an array defining any recognized media that was linked to within the
     * text (self post) portion of this link. Reddit is able to identify media
     * types and metadata from itself, as well as from certain external sites
     * like YouTube, Twitter, etc.
     *
     * @param array $mediaMetadata
     * @return $this
     */
    protected function setMediaMetadata(array $mediaMetadata = null) {
        $this->mediaMetadata = $mediaMetadata;
        return $this;
    }

    /**
     * Get whether or not this link contains media only (?) This hasn't been
     * observed set to true.
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getMediaOnly(): bool {
        return $this->mediaOnly;
    }

    /**
     * Set whether or not this link contains media only (?) This hasn't been
     * observed set to true.
     *
     * @param bool $mediaOnly
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setMediaOnly(bool $mediaOnly) {
        $this->mediaOnly = $mediaOnly;
        return $this;
    }

    /**
     * If this link was removed by a moderator, and the mod filled out the
     * "Add a removal reason" dialog, get the text supplied by the moderator.
     * If this link hasn't been removed by a moderator or if no reason was
     * given, returns null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     */
    public function getModNote(): ?string {
        return $this->modNote;
    }

    /**
     * Set the "removal reason" note supplied by a moderator for removing this
     * link, if any
     *
     * @param string $modNote
     * @return $this
     */
    protected function setModNote(string $modNote = null) {
        $this->modNote = $modNote;
        return $this;
    }

    /**
     * If this link was removed by a moderator, and the mod filled out the
     * "Add a removal reason" dialog, get the username of that moderator.
     * If this link hasn't been removed by a moderator or if no reason was
     * given, returns null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     */
    public function getModReasonBy(): ?string {
        return $this->modReasonBy;
    }

    /**
     * Set the username of the moderator who wrote the "removal reason," if any
     *
     * @param string $modReasonBy
     * @return $this
     */
    protected function setModReasonBy(string $modReasonBy = null) {
        $this->modReasonBy = $modReasonBy;
        return $this;
    }

    /**
     * If this link was removed by a moderator, and the mod chose a canned
     * reason from the "Add a removal reason" dialog, get the title of the
     * canned reason. If this link hasn't been removed by a moderator, or if
     * the mod used a custom freeform removal message instead of a canned one,
     * returns null.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return string|null
     */
    public function getModReasonTitle(): ?string {
        return $this->modReasonTitle;
    }

    /**
     * Set the title of the canned "removal reason" used when a moderator
     * removed this link, if that applies
     *
     * @param string $modReasonTitle
     * @return $this
     */
    protected function setModReasonTitle(string $modReasonTitle = null) {
        $this->modReasonTitle = $modReasonTitle;
        return $this;
    }

    /**
     * Get an array defining any reports that have been lodged against this
     * link by moderators of the subreddit where it resides
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return array
     * @see getStewardReports()
     * @see getUserReports()
     */
    public function getModReports(): array {
        return $this->modReports;
    }

    /**
     * Set an array defining any reports that have been lodged against this
     * link by moderators of the subreddit where it resides
     *
     * @param array $modReports
     * @return $this
     */
    protected function setModReports(array $modReports) {
        $this->modReports = $modReports;
        return $this;
    }

    /**
     * Get the Reddit internal "thing" fullname of this link, e.g. "t3_cjqwv5"
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set the Reddit internal "thing" fullname of this link, e.g. "t3_cjqwv5"
     *
     * @param string $name
     * @return $this
     */
    protected function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get whether or not this link has a "no follow" flag set (?)
     *
     * @return bool
     * @todo verify what exactly this corresponds to
     */
    public function getNoFollow(): bool {
        return $this->noFollow;
    }

    /**
     * Set whether or not this link has a "no follow" flag set (?)
     *
     * @param bool $noFollow
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setNoFollow(bool $noFollow) {
        $this->noFollow = $noFollow;
        return $this;
    }

    /**
     * Get the number of comments in this link's comment thread
     *
     * @return int
     */
    public function getNumComments(): int {
        return $this->numComments;
    }

    /**
     * Set the number of comments in this link's comment thread
     *
     * @param int $numComments
     * @return $this
     */
    protected function setNumComments(int $numComments) {
        $this->numComments = $numComments;
        return $this;
    }

    /**
     * Get the number of times this link has been crossposted to another subreddit
     *
     * @return int
     */
    public function getNumCrossposts(): int {
        return $this->numCrossposts;
    }

    /**
     * Set the number of times this link has been crossposted to another subreddit
     *
     * @param int $numCrossposts
     * @return $this
     */
    protected function setNumCrossposts(int $numCrossposts) {
        $this->numCrossposts = $numCrossposts;
        return $this;
    }

    /**
     * Get the number of user reports that have been lodged against this link.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return int|null
     */
    public function getNumReports(): ?int {
        return $this->numReports;
    }

    /**
     * Set the number of user reports that have been lodged against this link.
     *
     * @param int $numReports
     * @return $this
     */
    protected function setNumReports(int $numReports = null) {
        $this->numReports = $numReports;
        return $this;
    }

    /**
     * Get whether or not this link is marked with the "NSFW" tag
     *
     * @return bool
     */
    public function getOver18(): bool {
        return $this->over18;
    }

    /**
     * Set whether or not this link is marked with the "NSFW" tag
     *
     * @param bool $over18
     * @return $this
     */
    protected function setOver18(bool $over18) {
        $this->over18 = $over18;
        return $this;
    }

    /**
     * Get the advertising whitelist status for this link's subreddit, if set.
     * This indicates which types of ads are eligible to be displayed in the
     * subreddit. The link itself may have a different whitelist status; this
     * is typically the case when a link is marked NSFW but its subreddit is
     * not marked NSFW.
     *
     * @return string
     * @see getWhitelistStatus()
     */
    public function getParentWhitelistStatus(): string {
        return $this->parentWhitelistStatus;
    }

    /**
     * Set the advertising whitelist status for this link's subreddit, if set.
     *
     * @param string $parentWhitelistStatus
     * @return $this
     */
    protected function setParentWhitelistStatus(string $parentWhitelistStatus) {
        $this->parentWhitelistStatus = $parentWhitelistStatus;
        return $this;
    }

    /**
     * Get the portion of this link's permanent URL relative to https://reddit.com/
     *
     * @return string
     */
    public function getPermalink(): string {
        return $this->permalink;
    }

    /**
     * Set the portion of this link's permanent URL relative to https://reddit.com/
     *
     * @param string $permalink
     * @return $this
     */
    protected function setPermalink(string $permalink) {
        $this->permalink = $permalink;
        return $this;
    }

    /**
     * Get whether or not this link is pinned
     *
     * @return bool
     */
    public function getPinned(): bool {
        return $this->pinned;
    }

    /**
     * Set whether or not this link is pinned
     *
     * @param bool $pinned
     * @return $this
     */
    protected function setPinned(bool $pinned) {
        $this->pinned = $pinned;
        return $this;
    }

    /**
     * Get the post type hint (e.g. "image", "rich:video") set for this link,
     * if any.
     *
     * @return string
     */
    public function getPostHint(): string {
        return $this->postHint;
    }

    /**
     * Set the post type hint (e.g. "image", "rich:video") set for this link,
     * if any.
     *
     * @param string $postHint
     * @return $this
     */
    protected function setPostHint(string $postHint) {
        $this->postHint = $postHint;
        return $this;
    }

    /**
     * Get an array defining the different preview images available for this
     * link, if any.
     *
     * @return array
     */
    public function getPreview(): array {
        return $this->preview;
    }

    /**
     * Set an array defining the different preview images available for this
     * link, if any.
     *
     * @param array $preview
     * @return $this
     */
    protected function setPreview(array $preview) {
        $this->preview = $preview;
        return $this;
    }

    /**
     * Get the numeric whitelist status key for this link's subreddit, if set.
     * A text version of the corresponding whitelist status is available in
     * getParentWhitelistStatus()
     *
     * @return int
     * @see getParentWhitelistStatus()
     */
    public function getPwls(): int {
        return $this->pwls;
    }

    /**
     * Set the numeric whitelist status key for this link's subreddit, if set.
     *
     * @param int $pwls
     * @return $this
     */
    protected function setPwls(int $pwls) {
        $this->pwls = $pwls;
        return $this;
    }

    /**
     * Get whether or not this link is in a quarantined subreddit
     *
     * @return bool
     */
    public function getQuarantine(): bool {
        return $this->quarantine;
    }

    /**
     * Set whether or not this link is in a quarantined subreddit
     *
     * @param bool $quarantine
     * @return $this
     */
    protected function setQuarantine(bool $quarantine) {
        $this->quarantine = $quarantine;
        return $this;
    }

    /**
     * Get the reason this link was removed (?)
     *
     * When a link is removed by a moderator, the reason appears in mod_note;
     * the removal_reason field has not been observed to be populated
     *
     * @return string|null
     * @todo verify what exactly this corresponds to
     */
    public function getRemovalReason(): ?string {
        return $this->removalReason;
    }

    /**
     * Set the reason this link was removed (?)
     *
     * @param string $removalReason
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    protected function setRemovalReason(string $removalReason = null) {
        $this->removalReason = $removalReason;
        return $this;
    }

    /**
     * Get whether or not this link has been removed by a moderator
     *
     * @return bool|null
     */
    public function getRemoved(): ?bool {
        return $this->removed;
    }

    /**
     * Set whether or not this link has been removed by a moderator
     *
     * @param bool $removed
     * @return $this
     */
    public function setRemoved(bool $removed = null) {
        $this->removed = $removed;
        return $this;
    }

    /**
     * Per Reddit, this is deprecated and you should use mod_reports and
     * user_reports instead.
     *
     * @deprecated
     * @return array|null
     * @see getModReports()
     * @see getUserReports()
     */
    public function getReportReasons(): ?array {
        return $this->reportReasons;
    }

    /**
     * Per Reddit, this is deprecated and you should use mod_reports and
     * user_reports instead.
     *
     * @deprecated
     * @param array $reportReasons
     * @return $this
     * @see setModReports()
     * @see setUserReports()
     */
    protected function setReportReasons(array $reportReasons) {
        $this->reportReasons = $reportReasons;
        return $this;
    }

    /**
     * Get the rich text editor mode (e.g. "markdown") that was used to write
     * the text in this post, if any.
     *
     * This is an author-only property. To receive an accurate value, the
     * currently authenticated user must be the user who submitted the link.
     *
     * @return type
     */
    public function getRteMode() {
        return $this->rteMode;
    }

    /**
     * Set the rich text editor mode (e.g. "markdown") that was used to write
     * the text in this post, if any.
     *
     * @param type $rteMode
     * @return $this
     */
    public function setRteMode($rteMode) {
        $this->rteMode = $rteMode;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has saved this link
     *
     * @return bool
     */
    public function getSaved(): bool {
        return $this->saved;
    }

    /**
     * Set whether or not the currently authenticated user has saved this link
     *
     * @param bool $saved
     * @return $this
     */
    protected function setSaved(bool $saved) {
        $this->saved = $saved;
        return $this;
    }

    /**
     * Get the vote score of this link
     *
     * @return int
     */
    public function getScore(): int {
        return $this->score;
    }

    /**
     * Set the vote score of this link
     *
     * @param int $score
     * @return $this
     */
    protected function setScore(int $score) {
        $this->score = $score;
        return $this;
    }

    /**
     * Get an array defining any recognized media this link points to which is
     * served over TLS (https URLs). This may be a subset of getMedia(), or it
     * may be identical, if all of the recognized media is served securely.
     *
     * @return array|null
     * @see getMedia()
     */
    public function getSecureMedia(): ?array {
        return $this->secureMedia;
    }

    /**
     * Set an array defining any recognized media this link points to which is
     * served over TLS (https URLs).
     *
     * @param array $secureMedia
     * @return $this
     */
    protected function setSecureMedia(array $secureMedia = null) {
        $this->secureMedia = $secureMedia;
        return $this;
    }

    /**
     * Get an array defining the HTML properties required to embed recognized
     * media objects which are served over TLS (https URLs). This may be a
     * subset of getMediaEmbed(), or it may be identical, if all of the
     * recognized media is served securely.
     *
     * @return array
     * @see getMediaEmbed()
     */
    public function getSecureMediaEmbed(): array {
        return $this->secureMediaEmbed;
    }

    /**
     * Set an array defining the HTML properties required to embed recognized
     * media objects which are served over TLS (https URLs)
     *
     * @param array $secureMediaEmbed
     * @return $this
     */
    protected function setSecureMediaEmbed(array $secureMediaEmbed) {
        $this->secureMediaEmbed = $secureMediaEmbed;
        return $this;
    }

    /**
     * If this link is a text-only self post, get the text that comprises it
     *
     * @return string
     */
    public function getSelftext(): string {
        return $this->selftext;
    }

    /**
     * If this link is a text-only self post, set the text that comprises it
     *
     * @param string $selftext
     * @return $this
     */
    protected function setSelftext(string $selftext) {
        $this->selftext = $selftext;
        return $this;
    }

    /**
     * If this link is a text-only self post, get the HTML required to render
     * the text that comprises it
     *
     * @return string
     */
    public function getSelftextHtml(): string {
        return $this->selftextHtml;
    }

    /**
     * If this link is a text-only self post, set the HTML required to render
     * the text that comprises it
     *
     * @param string $selftextHtml
     * @return $this
     */
    protected function setSelftextHtml(string $selftextHtml) {
        $this->selftextHtml = $selftextHtml;
        return $this;
    }

    /**
     * Get whether or not "inbox replies" to the author are enabled on this link
     *
     * @return bool
     */
    public function getSendReplies(): bool {
        return $this->sendReplies;
    }

    /**
     * Set whether or not "inbox replies" to the author are enabled on this link
     *
     * @param bool $sendReplies
     * @return $this
     */
    protected function setSendReplies(bool $sendReplies) {
        $this->sendReplies = $sendReplies;
        return $this;
    }

    /**
     * Get whether or not a moderator has flagged this link as spam.
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return bool|null
     */
    public function getSpam(): ?bool {
        return $this->spam;
    }

    /**
     * Set whether or not a moderator has flagged this link as spam.
     *
     * @param bool $spam
     * @return $this
     */
    public function setSpam(bool $spam = null) {
        $this->spam = $spam;
        return $this;
    }

    /**
     * Get whether or not this link is marked with the "spoiler" tag
     *
     * @return bool
     */
    public function getSpoiler(): bool {
        return $this->spoiler;
    }

    /**
     * Set whether or not this link is marked with the "spoiler" tag
     *
     * @param bool $spoiler
     * @return $this
     */
    protected function setSpoiler(bool $spoiler) {
        $this->spoiler = $spoiler;
        return $this;
    }

    /**
     * Get an array defining any reports that have been lodged against this
     * link by... stewards? (Can't find an announcement of this as of 20190908)
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return array
     * @see getModReports()
     * @see getUserReports()
     * @todo verify what exactly this corresponds to
     */
    public function getStewardReports(): array {
        return $this->stewardReports;
    }

    /**
     * Set an array defining any reports that have been lodged against this
     * link by... stewards? (Can't find an announcement of this as of 20190908)
     *
     * @param array $stewardReports
     * @return $this
     * @todo verify what exactly this corresponds to
     */
    public function setStewardReports(array $stewardReports) {
        $this->stewardReports = $stewardReports;
        return $this;
    }

    /**
     * Get whether or not this link is stickied at the top of the subreddit
     *
     * @return bool
     */
    public function getStickied(): bool {
        return $this->stickied;
    }

    /**
     * Set whether or not this link is stickied at the top of the subreddit
     *
     * @param bool $stickied
     * @return $this
     */
    protected function setStickied(bool $stickied) {
        $this->stickied = $stickied;
        return $this;
    }

    /**
     * Get the name of the subreddit this link resides in
     *
     * @return string
     */
    public function getSubreddit(): string {
        return $this->subreddit;
    }

    /**
     * Set the name of the subreddit this link resides in
     *
     * @param string $subreddit
     * @return $this
     */
    protected function setSubreddit(string $subreddit) {
        $this->subreddit = $subreddit;
        return $this;
    }

    /**
     * Get the Reddit internal "thing" fullname of the subreddit this link
     * resides in, e.g. "t5_2qi1v"
     *
     * @return string
     */
    public function getSubredditId(): string {
        return $this->subredditId;
    }

    /**
     * Set the Reddit internal "thing" fullname of the subreddit this link
     * resides in, e.g. "t5_2qi1v"
     *
     * @param string $subredditId
     * @return $this
     */
    protected function setSubredditId(string $subredditId) {
        $this->subredditId = $subredditId;
        return $this;
    }

    /**
     * Get the name of the subreddit this link resides in, with its relative
     * path e.g. "r/funny", or "u/joe" for user profile subreddits
     *
     * @return string
     */
    public function getSubredditNamePrefixed(): string {
        return $this->subredditNamePrefixed;
    }

    /**
     * Set the name of the subreddit this link resides in, with its relative
     * path e.g. "r/funny", or "u/joe" for user profile subreddits
     *
     * @param string $subredditNamePrefixed
     * @return $this
     */
    protected function setSubredditNamePrefixed(string $subredditNamePrefixed) {
        $this->subredditNamePrefixed = $subredditNamePrefixed;
        return $this;
    }

    /**
     * Get the number of subscribers in the subreddit where this link resides
     *
     * @return int
     */
    public function getSubredditSubscribers(): int {
        return $this->subredditSubscribers;
    }

    /**
     * Set the number of subscribers in the subreddit where this link resides
     *
     * @param int $subredditSubscribers
     * @return $this
     */
    protected function setSubredditSubscribers(int $subredditSubscribers) {
        $this->subredditSubscribers = $subredditSubscribers;
        return $this;
    }

    /**
     * Get the type of access control enforced on the subreddit where this link
     * resides. This should be one of ['public', 'restricted', 'private',
     * 'employees_only']
     *
     * @return string
     */
    public function getSubredditType(): string {
        return $this->subredditType;
    }

    /**
     * Set the type of access control enforced on the subreddit where this link
     * resides. This should be one of ['public', 'restricted', 'private',
     * 'employees_only']
     *
     * @param string $subredditType
     * @return $this
     */
    protected function setSubredditType(string $subredditType) {
        $this->subredditType = $subredditType;
        return $this;
    }

    /**
     * Get the suggested sort order for comments on this link, if any has been
     * set
     *
     * @return string|null
     */
    public function getSuggestedSort(): ?string {
        return $this->suggestedSort;
    }

    /**
     * Set the suggested sort order for comments on this link, if any
     *
     * @param string $suggestedSort
     * @return $this
     */
    protected function setSuggestedSort(string $suggestedSort = null) {
        $this->suggestedSort = $suggestedSort;
        return $this;
    }

    /**
     * Get the URL to the thumbnail image for this link, if any
     *
     * @return string
     */
    public function getThumbnail(): string {
        return $this->thumbnail;
    }

    /**
     * Set the URL to the thumbnail image for this link, if any
     *
     * @param string $thumbnail
     * @return $this
     */
    protected function setThumbnail(string $thumbnail) {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * Get the thumbnail height pixel value, if any
     *
     * @return int|null
     */
    public function getThumbnailHeight(): ?int {
        return $this->thumbnailHeight;
    }

    /**
     * Set the thumbnail height pixel value, if any
     *
     * @param type $thumbnailHeight
     * @return $this
     */
    protected function setThumbnailHeight($thumbnailHeight = null) {
        $this->thumbnailHeight = $thumbnailHeight;
        return $this;
    }

    /**
     * Get the thumbnail width pixel value, if any
     *
     * @return int|null
     */
    public function getThumbnailWidth(): ?int {
        return $this->thumbnailWidth;
    }

    /**
     * Set the thumbnail width pixel value, if any
     *
     * @param type $thumbnailWidth
     * @return $this
     */
    protected function setThumbnailWidth($thumbnailWidth = null) {
        $this->thumbnailWidth = $thumbnailWidth;
        return $this;
    }

    /**
     * Get this link's title
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Set this link's title
     *
     * @param string $title
     * @return $this
     */
    protected function setTitle(string $title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the total number of awards (gold, platinum, etc.) this link has
     * received
     *
     * @return int
     */
    public function getTotalAwardsReceived(): int {
        return $this->totalAwardsReceived;
    }

    /**
     * Set the total number of awards (gold, platinum, etc.) this link has
     * received
     *
     * @param int $totalAwardsReceived
     * @return $this
     */
    protected function setTotalAwardsReceived(int $totalAwardsReceived) {
        $this->totalAwardsReceived = $totalAwardsReceived;
        return $this;
    }

    /**
     * Get the number of upvotes that have been recorded on this link
     *
     * @return int
     */
    public function getUps(): int {
        return $this->ups;
    }

    /**
     * Set the number of upvotes that have been recorded on this link
     *
     * @param int $ups
     * @return $this
     */
    protected function setUps(int $ups) {
        $this->ups = $ups;
        return $this;
    }

    /**
     * Get the URL to the target of this link. Text-only self posts will
     * return a link to their own comment thread.
     *
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Set the URL to the target of this link.
     *
     * @param string $url
     * @return $this
     */
    protected function setUrl(string $url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Get an array defining any reports that have been lodged against this
     * link by users. (Reports from moderators are stored separately in
     * mod_reports.)
     *
     * This is a moderator-only property. To receive an accurate value, the
     * currently authenticated user must be a moderator of the subreddit where
     * this link is posted.
     *
     * @return array
     * @see getModReports()
     * @see getStewardReports()
     */
    public function getUserReports(): array {
        return $this->userReports;
    }

    /**
     * Set an array defining any reports that have been lodged against this
     * link by users. (Reports from moderators are stored separately in
     * mod_reports.)
     *
     * @param array $userReports
     * @return $this
     */
    protected function setUserReports(array $userReports) {
        $this->userReports = $userReports;
        return $this;
    }

    /**
     * Get the number of times this link has been viewed.
     *
     * (Or, don't. This attribute is not returned by the API.)
     *
     * @return int|null
     */
    public function getViewCount(): ?int {
        return $this->viewCount;
    }

    /**
     * Set the number of times this link has been viewed.
     *
     * @param int $viewCount
     * @return $this
     */
    protected function setViewCount(int $viewCount = null) {
        $this->viewCount = $viewCount;
        return $this;
    }

    /**
     * Get whether or not the currently authenticated user has visited this
     * link. This will only be true if the user has gold, and has enabled the
     * "remember links I've visited across computers" gold-only preference.
     *
     * @return bool
     */
    public function getVisited(): bool {
        return $this->visited;
    }

    /**
     * Set whether or not the currently authenticated user has visited this
     * link.
     *
     * @param bool $visited
     * @return $this
     */
    protected function setVisited(bool $visited) {
        $this->visited = $visited;
        return $this;
    }

    /**
     * Get the advertising whitelist status for this link, if set. This
     * indicates which types of ads are eligible to be displayed.
     *
     * @return string
     */
    public function getWhitelistStatus(): string {
        return $this->whitelistStatus;
    }

    /**
     * Set the advertising whitelist status for this link, if set. This
     * indicates which types of ads are eligible to be displayed.
     *
     * @param string $whitelistStatus
     * @return $this
     */
    protected function setWhitelistStatus(string $whitelistStatus) {
        $this->whitelistStatus = $whitelistStatus;
        return $this;
    }

    /**
     * Get the numeric whitelist status key, if any. A text version of the
     * corresponding whitelist status is available in getWhitelistStatus()
     *
     * @return int
     * @see getWhitelistStatus()
     */
    public function getWls(): int {
        return $this->wls;
    }

    /**
     * Set the numeric whitelist status key, if any. A text version of the
     * corresponding whitelist status is available in getWhitelistStatus()
     *
     * @param int $wls
     * @return $this
     */
    protected function setWls(int $wls) {
        $this->wls = $wls;
        return $this;
    }

}
