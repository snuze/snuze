<?php

declare(strict_types=1);

namespace snuze\Persistence\MySQL;

use snuze\{
    Persistence\Interfaces\LinkMapperInterface,
    Persistence\Interfaces\StorageProviderInterface,
    Persistence\MySQL\StorageProvider,
    Reddit\Thing\Link
};

/**
 * A data mapper class for Link objects using the MySQL StorageProvider.
 *
 * This is where abstraction goes to die.
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
class LinkMapper extends \snuze\SnuzeObject implements LinkMapperInterface
{

    /**
     * A place to stash the PDO connection from the StorageProvider we're given
     *
     * @var \PDO
     */
    private $pdo = null;

    /**
     *
     * @param \snuze\Persistence\MySQL\StorageProvider $storage A
     *      MySQL StorageProvider object
     * @throws \snuze\Exception\ArgumentException
     */
    public function __construct(StorageProviderInterface $storage) {
        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Test that the storage provider is of the expected type */
        if (!$storage instanceof StorageProvider) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A ' . StorageProvider::class . ' object must be supplied');
        }

        /*
         * Set the local PDO property; the MySQL Mapper implementations don't
         * use anything else from the StorageProvider
         */
        $this->pdo = $storage->getPdo();
    }

    /**
     * Insert or update a Link to the MySQL database.
     *
     * @param \snuze\Reddit\Thing\Link $link The Link
     *      to persist
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function persist($link): bool {

        /* This mapper only knows how to persist one thing */
        if (!$link instanceof Link) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A Link object must be supplied');
        }
        $this->debug('Persisting Link ' . $link->getFullname());

        /* Ensure the object is sufficiently populated to persist */
        foreach (['id', 'author', 'subreddit', 'subredditId'] as $property) {
            $fn = 'get' . $property;
            if (empty($link->$fn())) {
                throw new \snuze\Exception\PersistenceException($this,
                        "Can't store Link with empty property: {$property}");
            }
        }

        /* Attempt to save this Link to the MySQL database */
        $query = <<<EOT
                INSERT INTO links (
                    id
                    ,all_awardings
                    ,allow_live_comments
                    ,approved
                    ,approved_at_utc
                    ,approved_by
                    ,archived
                    ,author
                    ,author_cakeday
                    ,author_flair_background_color
                    ,author_flair_css_class
                    ,author_flair_richtext
                    ,author_flair_template_id
                    ,author_flair_text
                    ,author_flair_text_color
                    ,author_flair_type
                    ,author_fullname
                    ,author_patreon_flair
                    ,banned_at_utc
                    ,banned_by
                    ,can_gild
                    ,can_mod_post
                    ,category
                    ,clicked
                    ,collections
                    ,content_categories
                    ,contest_mode
                    ,created
                    ,created_utc
                    ,crosspost_parent
                    ,crosspost_parent_list
                    ,discussion_type
                    ,distinguished
                    ,domain
                    ,downs
                    ,edited
                    ,event_end
                    ,event_is_live
                    ,event_start
                    ,gilded
                    ,gildings
                    ,hidden
                    ,hide_score
                    ,ignore_reports
                    ,is_crosspostable
                    ,is_meta
                    ,is_original_content
                    ,is_reddit_media_domain
                    ,is_robot_indexable
                    ,is_self
                    ,is_video
                    ,likes
                    ,link_flair_background_color
                    ,link_flair_css_class
                    ,link_flair_richtext
                    ,link_flair_template_id
                    ,link_flair_text
                    ,link_flair_text_color
                    ,link_flair_type
                    ,locked
                    ,media
                    ,media_embed
                    ,media_metadata
                    ,media_only
                    ,mod_note
                    ,mod_reason_by
                    ,mod_reason_title
                    ,mod_reports
                    ,name
                    ,no_follow
                    ,num_comments
                    ,num_crossposts
                    ,num_reports
                    ,over_18
                    ,parent_whitelist_status
                    ,permalink
                    ,pinned
                    ,post_hint
                    ,preview
                    ,pwls
                    ,quarantine
                    ,removal_reason
                    ,removed
                    ,report_reasons
                    ,rte_mode
                    ,saved
                    ,score
                    ,secure_media
                    ,secure_media_embed
                    ,selftext
                    ,selftext_html
                    ,send_replies
                    ,spam
                    ,spoiler
                    ,steward_reports
                    ,stickied
                    ,subreddit
                    ,subreddit_id
                    ,subreddit_name_prefixed
                    ,subreddit_subscribers
                    ,subreddit_type
                    ,suggested_sort
                    ,thumbnail
                    ,thumbnail_height
                    ,thumbnail_width
                    ,title
                    ,total_awards_received
                    ,ups
                    ,url
                    ,user_reports
                    ,view_count
                    ,visited
                    ,whitelist_status
                    ,wls
                )
                VALUES (
                    :id
                    ,:all_awardings
                    ,:allow_live_comments
                    ,:approved
                    ,:approved_at_utc
                    ,:approved_by
                    ,:archived
                    ,:author
                    ,:author_cakeday
                    ,:author_flair_background_color
                    ,:author_flair_css_class
                    ,:author_flair_richtext
                    ,:author_flair_template_id
                    ,:author_flair_text
                    ,:author_flair_text_color
                    ,:author_flair_type
                    ,:author_fullname
                    ,:author_patreon_flair
                    ,:banned_at_utc
                    ,:banned_by
                    ,:can_gild
                    ,:can_mod_post
                    ,:category
                    ,:clicked
                    ,:collections
                    ,:content_categories
                    ,:contest_mode
                    ,:created
                    ,:created_utc
                    ,:crosspost_parent
                    ,:crosspost_parent_list
                    ,:discussion_type
                    ,:distinguished
                    ,:domain
                    ,:downs
                    ,:edited
                    ,:event_end
                    ,:event_is_live
                    ,:event_start
                    ,:gilded
                    ,:gildings
                    ,:hidden
                    ,:hide_score
                    ,:ignore_reports
                    ,:is_crosspostable
                    ,:is_meta
                    ,:is_original_content
                    ,:is_reddit_media_domain
                    ,:is_robot_indexable
                    ,:is_self
                    ,:is_video
                    ,:likes
                    ,:link_flair_background_color
                    ,:link_flair_css_class
                    ,:link_flair_richtext
                    ,:link_flair_template_id
                    ,:link_flair_text
                    ,:link_flair_text_color
                    ,:link_flair_type
                    ,:locked
                    ,:media
                    ,:media_embed
                    ,:media_metadata
                    ,:media_only
                    ,:mod_note
                    ,:mod_reason_by
                    ,:mod_reason_title
                    ,:mod_reports
                    ,:name
                    ,:no_follow
                    ,:num_comments
                    ,:num_crossposts
                    ,:num_reports
                    ,:over_18
                    ,:parent_whitelist_status
                    ,:permalink
                    ,:pinned
                    ,:post_hint
                    ,:preview
                    ,:pwls
                    ,:quarantine
                    ,:removal_reason
                    ,:removed
                    ,:report_reasons
                    ,:rte_mode
                    ,:saved
                    ,:score
                    ,:secure_media
                    ,:secure_media_embed
                    ,:selftext
                    ,:selftext_html
                    ,:send_replies
                    ,:spam
                    ,:spoiler
                    ,:steward_reports
                    ,:stickied
                    ,:subreddit
                    ,:subreddit_id
                    ,:subreddit_name_prefixed
                    ,:subreddit_subscribers
                    ,:subreddit_type
                    ,:suggested_sort
                    ,:thumbnail
                    ,:thumbnail_height
                    ,:thumbnail_width
                    ,:title
                    ,:total_awards_received
                    ,:ups
                    ,:url
                    ,:user_reports
                    ,:view_count
                    ,:visited
                    ,:whitelist_status
                    ,:wls
                )
                ON DUPLICATE KEY UPDATE
                    all_awardings = VALUES(all_awardings)
                    ,allow_live_comments = VALUES(allow_live_comments)
                    ,approved = VALUES(approved)
                    ,approved_at_utc = VALUES(approved_at_utc)
                    ,approved_by = VALUES(approved_by)
                    ,archived = VALUES(archived)
                    ,author = VALUES(author)
                    ,author_cakeday = VALUES(author_cakeday)
                    ,author_flair_background_color = VALUES(author_flair_background_color)
                    ,author_flair_css_class = VALUES(author_flair_css_class)
                    ,author_flair_richtext = VALUES(author_flair_richtext)
                    ,author_flair_template_id = VALUES(author_flair_template_id)
                    ,author_flair_text = VALUES(author_flair_text)
                    ,author_flair_text_color = VALUES(author_flair_text_color)
                    ,author_flair_type = VALUES(author_flair_type)
                    ,author_fullname = VALUES(author_fullname)
                    ,author_patreon_flair = VALUES(author_patreon_flair)
                    ,banned_at_utc = VALUES(banned_at_utc)
                    ,banned_by = VALUES(banned_by)
                    ,can_gild = VALUES(can_gild)
                    ,can_mod_post = VALUES(can_mod_post)
                    ,category = VALUES(category)
                    ,clicked = VALUES(clicked)
                    ,collections = VALUES(collections)
                    ,content_categories = VALUES(content_categories)
                    ,contest_mode = VALUES(contest_mode)
                    ,created = VALUES(created)
                    ,created_utc = VALUES(created_utc)
                    ,crosspost_parent = VALUES(crosspost_parent)
                    ,crosspost_parent_list = VALUES(crosspost_parent_list)
                    ,discussion_type = VALUES(discussion_type)
                    ,distinguished = VALUES(distinguished)
                    ,domain = VALUES(domain)
                    ,downs = VALUES(downs)
                    ,edited = VALUES(edited)
                    ,event_end = VALUES(event_end)
                    ,event_is_live = VALUES(event_is_live)
                    ,event_start = VALUES(event_start)
                    ,gilded = VALUES(gilded)
                    ,gildings = VALUES(gildings)
                    ,hidden = VALUES(hidden)
                    ,hide_score = VALUES(hide_score)
                    ,ignore_reports = VALUES(ignore_reports)
                    ,is_crosspostable = VALUES(is_crosspostable)
                    ,is_meta = VALUES(is_meta)
                    ,is_original_content = VALUES(is_original_content)
                    ,is_reddit_media_domain = VALUES(is_reddit_media_domain)
                    ,is_robot_indexable = VALUES(is_robot_indexable)
                    ,is_self = VALUES(is_self)
                    ,is_video = VALUES(is_video)
                    ,likes = VALUES(likes)
                    ,link_flair_background_color = VALUES(link_flair_background_color)
                    ,link_flair_css_class = VALUES(link_flair_css_class)
                    ,link_flair_richtext = VALUES(link_flair_richtext)
                    ,link_flair_template_id = VALUES(link_flair_template_id)
                    ,link_flair_text = VALUES(link_flair_text)
                    ,link_flair_text_color = VALUES(link_flair_text_color)
                    ,link_flair_type = VALUES(link_flair_type)
                    ,locked = VALUES(locked)
                    ,media = VALUES(media)
                    ,media_embed = VALUES(media_embed)
                    ,media_metadata = VALUES(media_metadata)
                    ,media_only = VALUES(media_only)
                    ,mod_note = VALUES(mod_note)
                    ,mod_reason_by = VALUES(mod_reason_by)
                    ,mod_reason_title = VALUES(mod_reason_title)
                    ,mod_reports = VALUES(mod_reports)
                    ,name = VALUES(name)
                    ,no_follow = VALUES(no_follow)
                    ,num_comments = VALUES(num_comments)
                    ,num_crossposts = VALUES(num_crossposts)
                    ,num_reports = VALUES(num_reports)
                    ,over_18 = VALUES(over_18)
                    ,parent_whitelist_status = VALUES(parent_whitelist_status)
                    ,permalink = VALUES(permalink)
                    ,pinned = VALUES(pinned)
                    ,post_hint = VALUES(post_hint)
                    ,preview = VALUES(preview)
                    ,pwls = VALUES(pwls)
                    ,quarantine = VALUES(quarantine)
                    ,removal_reason = VALUES(removal_reason)
                    ,removed = VALUES(removed)
                    ,report_reasons = VALUES(report_reasons)
                    ,rte_mode = VALUES(rte_mode)
                    ,saved = VALUES(saved)
                    ,score = VALUES(score)
                    ,secure_media = VALUES(secure_media)
                    ,secure_media_embed = VALUES(secure_media_embed)
                    ,selftext = VALUES(selftext)
                    ,selftext_html = VALUES(selftext_html)
                    ,send_replies = VALUES(send_replies)
                    ,spam = VALUES(spam)
                    ,spoiler = VALUES(spoiler)
                    ,steward_reports = VALUES(steward_reports)
                    ,stickied = VALUES(stickied)
                    ,subreddit = VALUES(subreddit)
                    ,subreddit_id = VALUES(subreddit_id)
                    ,subreddit_name_prefixed = VALUES(subreddit_name_prefixed)
                    ,subreddit_subscribers = VALUES(subreddit_subscribers)
                    ,subreddit_type = VALUES(subreddit_type)
                    ,suggested_sort = VALUES(suggested_sort)
                    ,thumbnail = VALUES(thumbnail)
                    ,thumbnail_height = VALUES(thumbnail_height)
                    ,thumbnail_width = VALUES(thumbnail_width)
                    ,title = VALUES(title)
                    ,total_awards_received = VALUES(total_awards_received)
                    ,ups = VALUES(ups)
                    ,url = VALUES(url)
                    ,user_reports = VALUES(user_reports)
                    ,view_count = VALUES(view_count)
                    ,visited = VALUES(visited)
                    ,whitelist_status = VALUES(whitelist_status)
                    ,wls = VALUES(wls)
EOT;
        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $link->getId(), \PDO::PARAM_STR);
            $stmt->bindValue(':all_awardings',
                    is_null($link->getAllAwardings()) ? null : json_encode($link->getAllAwardings(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':allow_live_comments',
                    $link->getAllowLiveComments(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':approved', $link->getApproved(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':approved_at_utc', $link->getApprovedAtUtc(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':approved_by', $link->getApprovedBy(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':archived', $link->getArchived(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':author', $link->getAuthor(), \PDO::PARAM_STR);
            $stmt->bindValue(':author_cakeday', $link->getAuthorCakeday(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':author_flair_background_color',
                    $link->getAuthorFlairBackgroundColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_css_class',
                    $link->getAuthorFlairCssClass(), \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_richtext',
                    is_null($link->getAuthorFlairRichtext()) ? null : json_encode($link->getAuthorFlairRichtext(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_template_id',
                    $link->getAuthorFlairTemplateId(), \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_text', $link->getAuthorFlairText(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_text_color',
                    $link->getAuthorFlairTextColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':author_flair_type', $link->getAuthorFlairType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':author_fullname', $link->getAuthorFullname(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':author_patreon_flair',
                    $link->getAuthorPatreonFlair(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':banned_at_utc', $link->getBannedAtUtc(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':banned_by', $link->getBannedBy(), \PDO::PARAM_STR);
            $stmt->bindValue(':can_gild', $link->getCanGild(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':can_mod_post', $link->getCanModPost(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':category', $link->getCategory(), \PDO::PARAM_STR);
            $stmt->bindValue(':clicked', $link->getClicked(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':collections',
                    is_null($link->getCollections()) ? null : json_encode($link->getCollections(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':content_categories',
                    is_null($link->getContentCategories()) ? null : json_encode($link->getContentCategories(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':contest_mode', $link->getContestMode(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':created', $link->getCreated(), \PDO::PARAM_INT);
            $stmt->bindValue(':created_utc', $link->getCreatedUtc(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':crosspost_parent', $link->getCrosspostParent(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':crosspost_parent_list',
                    is_null($link->getCrosspostParentList()) ? null : json_encode($link->getCrosspostParentList(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':discussion_type', $link->getDiscussionType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':distinguished', $link->getDistinguished(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':domain', $link->getDomain(), \PDO::PARAM_STR);
            $stmt->bindValue(':downs', $link->getDowns(), \PDO::PARAM_INT);
            $stmt->bindValue(':edited', $link->getEdited(), \PDO::PARAM_INT);
            $stmt->bindValue(':event_end', $link->getEventEnd(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':event_is_live', $link->getEventIsLive(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':event_start', $link->getEventStart(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':gilded', $link->getGilded(), \PDO::PARAM_INT);
            $stmt->bindValue(':gildings',
                    is_null($link->getGildings()) ? null : json_encode($link->getGildings(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':hidden', $link->getHidden(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':hide_score', $link->getHideScore(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':ignore_reports', $link->getIgnoreReports(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_crosspostable', $link->getIsCrosspostable(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_meta', $link->getIsMeta(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_original_content',
                    $link->getIsOriginalContent(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_reddit_media_domain',
                    $link->getIsRedditMediaDomain(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_robot_indexable',
                    $link->getIsRobotIndexable(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_self', $link->getIsSelf(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':is_video', $link->getIsVideo(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':likes', $link->getLikes(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':link_flair_background_color',
                    $link->getLinkFlairBackgroundColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_css_class',
                    $link->getLinkFlairCssClass(), \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_richtext',
                    is_null($link->getLinkFlairRichtext()) ? null : json_encode($link->getLinkFlairRichtext(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_template_id',
                    $link->getLinkFlairTemplateId(), \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_text', $link->getLinkFlairText(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_text_color',
                    $link->getLinkFlairTextColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_type', $link->getLinkFlairType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':locked', $link->getLocked(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':media',
                    is_null($link->getMedia()) ? null : json_encode($link->getMedia(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':media_embed',
                    is_null($link->getMediaEmbed()) ? null : json_encode($link->getMediaEmbed(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':media_metadata',
                    is_null($link->getMediaMetadata()) ? null : json_encode($link->getMediaMetadata(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':media_only', $link->getMediaOnly(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':mod_note', $link->getModNote(), \PDO::PARAM_STR);
            $stmt->bindValue(':mod_reason_by', $link->getModReasonBy(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':mod_reason_title', $link->getModReasonTitle(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':mod_reports',
                    is_null($link->getModReports()) ? null : json_encode($link->getModReports(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':name', $link->getName(), \PDO::PARAM_STR);
            $stmt->bindValue(':no_follow', $link->getNoFollow(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':num_comments', $link->getNumComments(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':num_crossposts', $link->getNumCrossposts(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':num_reports', $link->getNumReports(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':over_18', $link->getOver18(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':parent_whitelist_status',
                    $link->getParentWhitelistStatus(), \PDO::PARAM_STR);
            $stmt->bindValue(':permalink', $link->getPermalink(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':pinned', $link->getPinned(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':post_hint', $link->getPostHint(), \PDO::PARAM_STR);
            $stmt->bindValue(':preview',
                    is_null($link->getPreview()) ? null : json_encode($link->getPreview(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':pwls', $link->getPwls(), \PDO::PARAM_INT);
            $stmt->bindValue(':quarantine', $link->getQuarantine(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':removal_reason', $link->getRemovalReason(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':removed', $link->getRemoved(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':report_reasons',
                    is_null($link->getReportReasons()) ? null : json_encode($link->getReportReasons(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':rte_mode', $link->getRteMode(), \PDO::PARAM_STR);
            $stmt->bindValue(':saved', $link->getSaved(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':score', $link->getScore(), \PDO::PARAM_INT);
            $stmt->bindValue(':secure_media',
                    is_null($link->getSecureMedia()) ? null : json_encode($link->getSecureMedia(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':secure_media_embed',
                    is_null($link->getSecureMediaEmbed()) ? null : json_encode($link->getSecureMediaEmbed(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':selftext', $link->getSelftext(), \PDO::PARAM_STR);
            $stmt->bindValue(':selftext_html', $link->getSelftextHtml(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':send_replies', $link->getSendReplies(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':spam', $link->getSpam(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':spoiler', $link->getSpoiler(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':steward_reports',
                    is_null($link->getstewardReports()) ? null : json_encode($link->getstewardReports(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':stickied', $link->getStickied(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':subreddit', $link->getSubreddit(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':subreddit_id', $link->getSubredditId(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':subreddit_name_prefixed',
                    $link->getSubredditNamePrefixed(), \PDO::PARAM_STR);
            $stmt->bindValue(':subreddit_subscribers',
                    $link->getSubredditSubscribers(), \PDO::PARAM_INT);
            $stmt->bindValue(':subreddit_type', $link->getSubredditType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':suggested_sort', $link->getSuggestedSort(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':thumbnail', $link->getThumbnail(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':thumbnail_height', $link->getThumbnailHeight(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':thumbnail_width', $link->getThumbnailWidth(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':title', $link->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':total_awards_received',
                    $link->getTotalAwardsReceived(), \PDO::PARAM_INT);
            $stmt->bindValue(':ups', $link->getUps(), \PDO::PARAM_INT);
            $stmt->bindValue(':url', $link->getUrl(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_reports',
                    is_null($link->getUserReports()) ? null : json_encode($link->getUserReports(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':view_count', $link->getViewCount(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':visited', $link->getVisited(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':whitelist_status', $link->getWhitelistStatus(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':wls', $link->getWls(), \PDO::PARAM_INT);

            $stmt->execute();
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        return true;
    }

    /**
     * Get a Link from the database by its id.
     *
     * @param string $id The Reddit internal identifier (e.g. "5bx4bx") of the
     *      Link to retrieve
     * @return \snuze\Reddit\Thing\Link|null
     */
    public function retrieve($id) {

        $query = <<<EOT
            SELECT * -- meh
            FROM links
            WHERE id = :id
EOT;

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("Requested link {$id} wasn't found");
            return null;
        }

        /**
         * Boolean fields are stored as BIT (0 or 1) in the database. Snuze
         * expects booleans, and uses strict typing. Here the 0s and 1s are
         * massaged back to boolean values.
         */
        $boolFields = [
            'allow_live_comments',
            'approved',
            'archived',
            'author_cakeday',
            'author_patreon_flair',
            'can_gild',
            'can_mod_post',
            'clicked',
            'contest_mode',
            'edited',
            'event_is_live',
            'hidden',
            'hide_score',
            'ignore_reports',
            'is_crosspostable',
            'is_meta',
            'is_original_content',
            'is_reddit_media_domain',
            'is_robot_indexable',
            'is_self',
            'is_video',
            'likes',
            'locked',
            'media_only',
            'no_follow',
            'over_18',
            'pinned',
            'quarantine',
            'removed',
            'saved',
            'send_replies',
            'spam',
            'spoiler',
            'stickied',
            'visited',
        ];
        foreach ($boolFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = (bool) $row[$field];
            }
        }

        /**
         * Array fields are stored in the database as JSON-encoded strings. Here
         * they're massaged back into arrays so json_encode() (below) doesn't
         * double-encode them.
         */
        $arrayFields = [
            'all_awardings',
            'author_flair_richtext',
            'collections',
            'content_categories',
            'crosspost_parent_list',
            'gildings',
            'link_flair_richtext',
            'media',
            'media_embed',
            'media_metadata',
            'mod_reports',
            'preview',
            'report_reasons',
            'secure_media',
            'secure_media_embed',
            'steward_reports',
            'user_reports',
        ];
        foreach ($arrayFields as $field) {
            if (!empty($row[$field])) {
                $row[$field] = json_decode($row[$field], true);
            }
        }

        /**
         * Reddit provides epoch timestamps as floats. They're stored in the
         * database as integers. Here they're massaged back into floats.
         */
        $floatFields = [
            'created',
            'created_utc',
            'edited',
            'event_end',
            'event_start'
        ];
        foreach ($floatFields as $field) {
            $row[$field] = (float) ($row[$field]);
        }

        /* Finally, wrap it all up in a Reddit "thing" style JSON package */
        $arr = [
            'kind' => 't3',
            'data' => $row
        ];

        /* Return a Link object */
        return (new Link())->fromJson(json_encode($arr,
                                JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
    }

    /**
     * Delete a Link from the database.
     *
     * @param \snuze\Reddit\Thing\Link $object The Link to delete
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function delete($object): bool {

        /* This mapper only knows how to delete one thing */
        if (!$object instanceof Link) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A Link object must be supplied');
        }
        $this->debug('Deleting Link ' . $object->getDisplayName());

        $query = 'DELETE FROM links WHERE id = :id';

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $object->getId(), \PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        return true;
    }

}
