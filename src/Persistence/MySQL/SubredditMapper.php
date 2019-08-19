<?php

declare(strict_types=1);

namespace snuze\Persistence\MySQL;

use snuze\{
    Persistence\Interfaces\SubredditMapperInterface,
    Persistence\Interfaces\StorageProviderInterface,
    Persistence\MySQL\StorageProvider,
    Reddit\Thing\Subreddit
};

/**
 * A data mapper class for Subreddit objects using the MySQL StorageProvider.
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
class SubredditMapper extends \snuze\SnuzeObject implements SubredditMapperInterface
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
     * Insert or update a Subreddit to the MySQL database.
     *
     * @param \snuze\Reddit\Thing\Subreddit $subreddit The Subreddit
     *      to persist
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function persist($subreddit): bool {

        /* This mapper only knows how to persist one thing */
        if (!$subreddit instanceof Subreddit) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A Subreddit object must be supplied');
        }
        $this->debug('Persisting Subreddit ' . $subreddit->getDisplayName());

        /* Ensure the object is sufficiently populated to persist */
        foreach (['id', 'name', 'displayName'] as $property) {
            $fn = 'get' . $property;
            if (empty($subreddit->$fn())) {
                throw new \snuze\Exception\PersistenceException($this,
                        "Can't store Subreddit with empty property: {$property}");
            }
        }

        /* Attempt to save this Subreddit to the MySQL database */
        $query = <<<EOT
                INSERT INTO subreddits (
                    id
                    ,display_name
                    ,accounts_active
                    ,accounts_active_is_fuzzed
                    ,active_user_count
                    ,advertiser_category
                    ,all_original_content
                    ,allow_discovery
                    ,allow_images
                    ,allow_videogifs
                    ,allow_videos
                    ,banner_background_color
                    ,banner_background_image
                    ,banner_img
                    ,banner_size
                    ,can_assign_link_flair
                    ,can_assign_user_flair
                    ,coins
                    ,collapse_deleted_comments
                    ,collections_enabled
                    ,comment_score_hide_mins
                    ,community_icon
                    ,content_category
                    ,created
                    ,created_utc
                    ,description
                    ,description_html
                    ,disable_contributor_requests
                    ,display_name_prefixed
                    ,emojis_custom_size
                    ,emojis_enabled
                    ,event_posts_enabled
                    ,free_form_reports
                    ,has_menu_widget
                    ,header_img
                    ,header_size
                    ,header_title
                    ,hide_ads
                    ,icon_img
                    ,icon_size
                    ,is_enrolled_in_new_modmail
                    ,key_color
                    ,lang
                    ,link_flair_enabled
                    ,link_flair_position
                    ,mobile_banner_image
                    ,name
                    ,notification_level
                    ,original_content_tag_enabled
                    ,over18
                    ,primary_color
                    ,public_description
                    ,public_description_html
                    ,public_traffic
                    ,quarantine
                    ,restrict_commenting
                    ,restrict_posting
                    ,show_media
                    ,show_media_preview
                    ,spoilers_enabled
                    ,submission_type
                    ,submit_link_label
                    ,submit_text
                    ,submit_text_html
                    ,submit_text_label
                    ,subreddit_type
                    ,subscribers
                    ,suggested_comment_sort
                    ,title
                    ,url
                    ,user_can_flair_in_sr
                    ,user_flair_background_color
                    ,user_flair_css_class
                    ,user_flair_enabled_in_sr
                    ,user_flair_position
                    ,user_flair_richtext
                    ,user_flair_template_id
                    ,user_flair_text
                    ,user_flair_text_color
                    ,user_flair_type
                    ,user_has_favorited
                    ,user_is_banned
                    ,user_is_contributor
                    ,user_is_moderator
                    ,user_is_muted
                    ,user_is_subscriber
                    ,user_sr_flair_enabled
                    ,user_sr_theme_enabled
                    ,videostream_links_count
                    ,whitelist_status
                    ,wiki_enabled
                    ,wls
                )
                VALUES (
                    :id
                    ,:display_name
                    ,:accounts_active
                    ,:accounts_active_is_fuzzed
                    ,:active_user_count
                    ,:advertiser_category
                    ,:all_original_content
                    ,:allow_discovery
                    ,:allow_images
                    ,:allow_videogifs
                    ,:allow_videos
                    ,:banner_background_color
                    ,:banner_background_image
                    ,:banner_img
                    ,:banner_size
                    ,:can_assign_link_flair
                    ,:can_assign_user_flair
                    ,:coins
                    ,:collapse_deleted_comments
                    ,:collections_enabled
                    ,:comment_score_hide_mins
                    ,:community_icon
                    ,:content_category
                    ,:created
                    ,:created_utc
                    ,:description
                    ,:description_html
                    ,:disable_contributor_requests
                    ,:display_name_prefixed
                    ,:emojis_custom_size
                    ,:emojis_enabled
                    ,:event_posts_enabled
                    ,:free_form_reports
                    ,:has_menu_widget
                    ,:header_img
                    ,:header_size
                    ,:header_title
                    ,:hide_ads
                    ,:icon_img
                    ,:icon_size
                    ,:is_enrolled_in_new_modmail
                    ,:key_color
                    ,:lang
                    ,:link_flair_enabled
                    ,:link_flair_position
                    ,:mobile_banner_image
                    ,:name
                    ,:notification_level
                    ,:original_content_tag_enabled
                    ,:over18
                    ,:primary_color
                    ,:public_description
                    ,:public_description_html
                    ,:public_traffic
                    ,:quarantine
                    ,:restrict_commenting
                    ,:restrict_posting
                    ,:show_media
                    ,:show_media_preview
                    ,:spoilers_enabled
                    ,:submission_type
                    ,:submit_link_label
                    ,:submit_text
                    ,:submit_text_html
                    ,:submit_text_label
                    ,:subreddit_type
                    ,:subscribers
                    ,:suggested_comment_sort
                    ,:title
                    ,:url
                    ,:user_can_flair_in_sr
                    ,:user_flair_background_color
                    ,:user_flair_css_class
                    ,:user_flair_enabled_in_sr
                    ,:user_flair_position
                    ,:user_flair_richtext
                    ,:user_flair_template_id
                    ,:user_flair_text
                    ,:user_flair_text_color
                    ,:user_flair_type
                    ,:user_has_favorited
                    ,:user_is_banned
                    ,:user_is_contributor
                    ,:user_is_moderator
                    ,:user_is_muted
                    ,:user_is_subscriber
                    ,:user_sr_flair_enabled
                    ,:user_sr_theme_enabled
                    ,:videostream_links_count
                    ,:whitelist_status
                    ,:wiki_enabled
                    ,:wls
                )
                ON DUPLICATE KEY UPDATE
                    accounts_active = VALUES(accounts_active)
                    ,accounts_active_is_fuzzed = VALUES(accounts_active_is_fuzzed)
                    ,active_user_count = VALUES(active_user_count)
                    ,advertiser_category = VALUES(advertiser_category)
                    ,all_original_content = VALUES(all_original_content)
                    ,allow_discovery = VALUES(allow_discovery)
                    ,allow_images = VALUES(allow_images)
                    ,allow_videogifs = VALUES(allow_videogifs)
                    ,allow_videos = VALUES(allow_videos)
                    ,banner_background_color = VALUES(banner_background_color)
                    ,banner_background_image = VALUES(banner_background_image)
                    ,banner_img = VALUES(banner_img)
                    ,banner_size = VALUES(banner_size)
                    ,can_assign_link_flair = VALUES(can_assign_link_flair)
                    ,can_assign_user_flair = VALUES(can_assign_user_flair)
                    ,coins = VALUES(coins)
                    ,collapse_deleted_comments = VALUES(collapse_deleted_comments)
                    ,collections_enabled = VALUES(collections_enabled)
                    ,comment_score_hide_mins = VALUES(comment_score_hide_mins)
                    ,community_icon = VALUES(community_icon)
                    ,content_category = VALUES(content_category)
                    ,created = VALUES(created)
                    ,created_utc = VALUES(created_utc)
                    ,description = VALUES(description)
                    ,description_html = VALUES(description_html)
                    ,disable_contributor_requests = VALUES(disable_contributor_requests)
                    ,display_name_prefixed = VALUES(display_name_prefixed)
                    ,emojis_custom_size = VALUES(emojis_custom_size)
                    ,emojis_enabled = VALUES(emojis_enabled)
                    ,event_posts_enabled = VALUES(event_posts_enabled)
                    ,free_form_reports = VALUES(free_form_reports)
                    ,has_menu_widget = VALUES(has_menu_widget)
                    ,header_img = VALUES(header_img)
                    ,header_size = VALUES(header_size)
                    ,header_title = VALUES(header_title)
                    ,hide_ads = VALUES(hide_ads)
                    ,icon_img = VALUES(icon_img)
                    ,icon_size = VALUES(icon_size)
                    ,is_enrolled_in_new_modmail = VALUES(is_enrolled_in_new_modmail)
                    ,key_color = VALUES(key_color)
                    ,lang = VALUES(lang)
                    ,link_flair_enabled = VALUES(link_flair_enabled)
                    ,link_flair_position = VALUES(link_flair_position)
                    ,mobile_banner_image = VALUES(mobile_banner_image)
                    ,name = VALUES(name)
                    ,notification_level = VALUES(notification_level)
                    ,original_content_tag_enabled = VALUES(original_content_tag_enabled)
                    ,over18 = VALUES(over18)
                    ,primary_color = VALUES(primary_color)
                    ,public_description = VALUES(public_description)
                    ,public_description_html = VALUES(public_description_html)
                    ,public_traffic = VALUES(public_traffic)
                    ,quarantine = VALUES(quarantine)
                    ,restrict_commenting = VALUES(restrict_commenting)
                    ,restrict_posting = VALUES(restrict_posting)
                    ,show_media = VALUES(show_media)
                    ,show_media_preview = VALUES(show_media_preview)
                    ,spoilers_enabled = VALUES(spoilers_enabled)
                    ,submission_type = VALUES(submission_type)
                    ,submit_link_label = VALUES(submit_link_label)
                    ,submit_text = VALUES(submit_text)
                    ,submit_text_html = VALUES(submit_text_html)
                    ,submit_text_label = VALUES(submit_text_label)
                    ,subreddit_type = VALUES(subreddit_type)
                    ,subscribers = VALUES(subscribers)
                    ,suggested_comment_sort = VALUES(suggested_comment_sort)
                    ,title = VALUES(title)
                    ,url = VALUES(url)
                    ,user_can_flair_in_sr = VALUES(user_can_flair_in_sr)
                    ,user_flair_background_color = VALUES(user_flair_background_color)
                    ,user_flair_css_class = VALUES(user_flair_css_class)
                    ,user_flair_enabled_in_sr = VALUES(user_flair_enabled_in_sr)
                    ,user_flair_position = VALUES(user_flair_position)
                    ,user_flair_richtext = VALUES(user_flair_richtext)
                    ,user_flair_template_id = VALUES(user_flair_template_id)
                    ,user_flair_text = VALUES(user_flair_text)
                    ,user_flair_text_color = VALUES(user_flair_text_color)
                    ,user_flair_type = VALUES(user_flair_type)
                    ,user_has_favorited = VALUES(user_has_favorited)
                    ,user_is_banned = VALUES(user_is_banned)
                    ,user_is_contributor = VALUES(user_is_contributor)
                    ,user_is_moderator = VALUES(user_is_moderator)
                    ,user_is_muted = VALUES(user_is_muted)
                    ,user_is_subscriber = VALUES(user_is_subscriber)
                    ,user_sr_flair_enabled = VALUES(user_sr_flair_enabled)
                    ,user_sr_theme_enabled = VALUES(user_sr_theme_enabled)
                    ,videostream_links_count = VALUES(videostream_links_count)
                    ,whitelist_status = VALUES(whitelist_status)
                    ,wiki_enabled = VALUES(wiki_enabled)
                    ,wls = VALUES(wls)
EOT;
        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $subreddit->getId(), \PDO::PARAM_STR);
            $stmt->bindValue(':display_name', $subreddit->getDisplayName(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':accounts_active',
                    $subreddit->getAccountsActive(), \PDO::PARAM_INT);
            $stmt->bindValue(':accounts_active_is_fuzzed',
                    $subreddit->getAccountsActiveIsFuzzed(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':active_user_count',
                    $subreddit->getActiveUserCount(), \PDO::PARAM_INT);
            $stmt->bindValue(':advertiser_category',
                    $subreddit->getAdvertiserCategory(), \PDO::PARAM_STR);
            $stmt->bindValue(':all_original_content',
                    $subreddit->getAllOriginalContent(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':allow_discovery',
                    $subreddit->getAllowDiscovery(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':allow_images', $subreddit->getAllowImages(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':allow_videogifs',
                    $subreddit->getAllowVideogifs(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':allow_videos', $subreddit->getAllowVideos(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':banner_background_color',
                    $subreddit->getBannerBackgroundColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':banner_background_image',
                    $subreddit->getBannerBackgroundImage(), \PDO::PARAM_STR);
            $stmt->bindValue(':banner_img', $subreddit->getBannerImg(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':banner_size',
                    is_null($subreddit->getBannerSize()) ? null : json_encode($subreddit->getBannerSize(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':can_assign_link_flair',
                    $subreddit->getCanAssignLinkFlair(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':can_assign_user_flair',
                    $subreddit->getCanAssignUserFlair(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':coins', $subreddit->getCoins(), \PDO::PARAM_INT);
            $stmt->bindValue(':collapse_deleted_comments',
                    $subreddit->getCollapseDeletedComments(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':collections_enabled',
                    $subreddit->getCollectionsEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':comment_score_hide_mins',
                    $subreddit->getCommentScoreHideMins(), \PDO::PARAM_INT);
            $stmt->bindValue(':community_icon', $subreddit->getCommunityIcon(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':content_category',
                    $subreddit->getContentCategory(), \PDO::PARAM_STR);
            $stmt->bindValue(':created', (int) $subreddit->getCreated(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':created_utc', (int) $subreddit->getCreatedUtc(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':description', $subreddit->getDescription(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':description_html',
                    $subreddit->getDescriptionHtml(), \PDO::PARAM_STR);
            $stmt->bindValue(':disable_contributor_requests',
                    $subreddit->getDisableContributorRequests(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':display_name_prefixed',
                    $subreddit->getDisplayNamePrefixed(), \PDO::PARAM_STR);
            $stmt->bindValue(':emojis_custom_size',
                    is_null($subreddit->getEmojisCustomSize()) ? null : json_encode($subreddit->getEmojisCustomSize(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':emojis_enabled', $subreddit->getEmojisEnabled(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':event_posts_enabled',
                    $subreddit->getEventPostsEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':free_form_reports',
                    $subreddit->getFreeFormReports(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':has_menu_widget', $subreddit->getHasMenuWidget(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':header_img', $subreddit->getHeaderImg(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':header_size',
                    is_null($subreddit->getHeaderSize()) ? null : json_encode($subreddit->getHeaderSize(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':header_title', $subreddit->getHeaderTitle(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':hide_ads', $subreddit->getHideAds(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':icon_img', $subreddit->getIconImg(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':icon_size',
                    is_null($subreddit->getIconSize()) ? null : json_encode($subreddit->getIconSize(),
                                    JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':is_enrolled_in_new_modmail',
                    $subreddit->getIsEnrolledInNewModmail(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':key_color', $subreddit->getKeyColor(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':lang', $subreddit->getLang(), \PDO::PARAM_STR);
            $stmt->bindValue(':link_flair_enabled',
                    $subreddit->getLinkFlairEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':link_flair_position',
                    $subreddit->getLinkFlairPosition(), \PDO::PARAM_STR);
            $stmt->bindValue(':mobile_banner_image',
                    $subreddit->getMobileBannerImage(), \PDO::PARAM_STR);
            $stmt->bindValue(':name', $subreddit->getName(), \PDO::PARAM_STR);
            $stmt->bindValue(':notification_level',
                    $subreddit->getNotificationLevel(), \PDO::PARAM_STR);
            $stmt->bindValue(':original_content_tag_enabled',
                    $subreddit->getOriginalContentTagEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':over18', $subreddit->getOver18(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':primary_color', $subreddit->getPrimaryColor(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':public_description',
                    $subreddit->getPublicDescription(), \PDO::PARAM_STR);
            $stmt->bindValue(':public_description_html',
                    $subreddit->getPublicDescriptionHtml(), \PDO::PARAM_STR);
            $stmt->bindValue(':public_traffic', $subreddit->getPublicTraffic(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':quarantine', $subreddit->getQuarantine(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':restrict_commenting',
                    $subreddit->getRestrictCommenting(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':restrict_posting',
                    $subreddit->getRestrictPosting(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':show_media', $subreddit->getShowMedia(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':show_media_preview',
                    $subreddit->getShowMediaPreview(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':spoilers_enabled',
                    $subreddit->getSpoilersEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':submission_type',
                    $subreddit->getSubmissionType(), \PDO::PARAM_STR);
            $stmt->bindValue(':submit_link_label',
                    $subreddit->getSubmitLinkLabel(), \PDO::PARAM_STR);
            $stmt->bindValue(':submit_text', $subreddit->getSubmitText(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':submit_text_html',
                    $subreddit->getSubmitTextHtml(), \PDO::PARAM_STR);
            $stmt->bindValue(':submit_text_label',
                    $subreddit->getSubmitTextLabel(), \PDO::PARAM_STR);
            $stmt->bindValue(':subreddit_type', $subreddit->getSubredditType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':subscribers', $subreddit->getSubscribers(),
                    \PDO::PARAM_INT);
            $stmt->bindValue(':suggested_comment_sort',
                    $subreddit->getSuggestedCommentSort(), \PDO::PARAM_STR);
            $stmt->bindValue(':title', $subreddit->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':url', $subreddit->getUrl(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_can_flair_in_sr',
                    $subreddit->getUserCanFlairInSr(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_flair_background_color',
                    $subreddit->getUserFlairBackgroundColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_css_class',
                    $subreddit->getUserFlairCssClass(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_enabled_in_sr',
                    $subreddit->getUserFlairEnabledInSr(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_flair_position',
                    $subreddit->getUserFlairPosition(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_richtext',
                    json_encode($subreddit->getUserFlairRichtext(),
                            JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_template_id',
                    $subreddit->getUserFlairTemplateId(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_text', $subreddit->getUserFlairText(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_text_color',
                    $subreddit->getUserFlairTextColor(), \PDO::PARAM_STR);
            $stmt->bindValue(':user_flair_type', $subreddit->getUserFlairType(),
                    \PDO::PARAM_STR);
            $stmt->bindValue(':user_has_favorited',
                    $subreddit->getUserHasFavorited(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_is_banned', $subreddit->getUserIsBanned(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_is_contributor',
                    $subreddit->getUserIsContributor(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_is_moderator',
                    $subreddit->getUserIsModerator(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_is_muted', $subreddit->getUserIsMuted(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_is_subscriber',
                    $subreddit->getUserIsSubscriber(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_sr_flair_enabled',
                    $subreddit->getUserSrFlairEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':user_sr_theme_enabled',
                    $subreddit->getUserSrThemeEnabled(), \PDO::PARAM_BOOL);
            $stmt->bindValue(':videostream_links_count',
                    $subreddit->getVideostreamLinksCount(), \PDO::PARAM_INT);
            $stmt->bindValue(':whitelist_status',
                    $subreddit->getWhitelistStatus(), \PDO::PARAM_STR);
            $stmt->bindValue(':wiki_enabled', $subreddit->getWikiEnabled(),
                    \PDO::PARAM_BOOL);
            $stmt->bindValue(':wls', $subreddit->getWls(), \PDO::PARAM_INT);

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
     * Get a Subreddit from the database by its display name.
     *
     * @param string $displayName The display name (e.g. "recipes") of the
     *      Subreddit to retrieve
     * @return \snuze\Reddit\Thing\Subreddit|null
     */
    public function retrieve($displayName) {

        $query = <<<EOT
            SELECT * -- meh
            FROM subreddits
            WHERE display_name = :display_name
EOT;

        try {
            /* Prepare and execute a statement */
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':display_name', $displayName, \PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $ex) {
            /* Re-throw as logged SnuzeException */
            throw new \snuze\Exception\PersistenceException($this,
                    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if (empty($row)) {
            $this->debug("Requested subreddit {$displayName} wasn't found");
            return null;
        }

        /**
         * Boolean fields are stored as BIT (0 or 1) in the database. Snuze
         * expects booleans, and uses strict typing. Here the 0s and 1s are
         * massaged back to boolean values.
         */
        $boolFields = [
            'accounts_active_is_fuzzed',
            'all_original_content',
            'allow_discovery',
            'allow_images',
            'allow_videogifs',
            'allow_videos',
            'can_assign_link_flair',
            'can_assign_user_flair',
            'collapse_deleted_comments',
            'collections_enabled',
            'disable_contributor_requests',
            'emojis_enabled',
            'event_posts_enabled',
            'free_form_reports',
            'has_menu_widget',
            'hide_ads',
            'is_enrolled_in_new_modmail',
            'link_flair_enabled',
            'original_content_tag_enabled',
            'over18',
            'public_traffic',
            'quarantine',
            'restrict_commenting',
            'restrict_posting',
            'show_media',
            'show_media_preview',
            'spoilers_enabled',
            'user_can_flair_in_sr',
            'user_flair_enabled_in_sr',
            'user_has_favorited',
            'user_is_banned',
            'user_is_contributor',
            'user_is_moderator',
            'user_is_muted',
            'user_is_subscriber',
            'user_sr_flair_enabled',
            'user_sr_theme_enabled',
            'wiki_enabled'
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
            'banner_size', 'emojis_custom_size', 'header_size', 'icon_size',
            'user_flair_richtext'
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
        foreach (['created', 'created_utc'] as $field) {
            $row[$field] = (float) ($row[$field]);
        }

        /* Finally, wrap it all up in a Reddit "thing" style JSON package */
        $arr = [
            'kind' => 't5',
            'data' => $row
        ];

        /* Return a Subreddit object */
        return (new Subreddit())->fromJson(json_encode($arr,
                                JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
    }

    /**
     * Delete a Subreddit from the database.
     *
     * @param \snuze\Reddit\Thing\Subreddit $object The Subreddit
     *      to delete
     * @return bool True on success; otherwise, an exception is thrown
     * @throws \snuze\Exception\ArgumentException
     * @throws \snuze\Exception\PersistenceException
     */
    public function delete($object): bool {

        /* This mapper only knows how to delete one thing */
        if (!$object instanceof Subreddit) {
            throw new \snuze\Exception\ArgumentException($this,
                    'A Subreddit object must be supplied');
        }
        $this->debug('Deleting Subreddit ' . $object->getDisplayName());

        $query = 'DELETE FROM subreddits WHERE id = :id';

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

    /**
     * Get a Subreddit from the database by its Reddit internal ID. This incurs
     * an extra query penalty, as it first looks up the corresponding display
     * name and then calls retrieve() to do the heavy lifting.
     *
     * @param string $id The Reddit internal ID (e.g. "2qh56") of the Subreddit
     *      to retrieve
     * @return \snuze\Reddit\Thing\Subreddit|null
     * @throws \snuze\Exception\PersistenceException
     */
    public function retrieveById(string $id): ?\snuze\Reddit\Thing\Subreddit {

        $query = 'SELECT displayName FROM subreddits WHERE id = :id';

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
            $this->debug("Requested subreddit {$id} wasn't found");
            return null;
        }

        return $this->retrieve($row['display_name']);
    }

}
