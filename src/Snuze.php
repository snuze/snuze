<?php

declare(strict_types=1);

namespace snuze;

/**
 * The Snuze object is the primary public interface to all of the functionality
 * this package provides. This is the object your application uses to interact
 * directly with the Reddit API and the storage provider (if one is enabled).
 * You get a Snuze object by passing an array of configuration parameters to
 * a SnuzeFactory and calling its getSnuze() method.
 *
 * To help elucidate when API requests are involved, methods that retrieve data
 * from Reddit's API have names beginning with "fetch" instead of "get." All
 * methods whose names begin with "fetch" will, when called, initiate at least
 * one outbound connection to the remote API server.
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
class Snuze extends SnuzeObject
{

    /**
     * The Snuze build number.
     */
    const VERSION = 1000706; /* 0.7.6 */

    /**
     * An AuthenticationState object. This holds the Reddit credentials and
     * the current status of the rate limiter.
     *
     * @var \snuze\AuthenticationState
     */
    private $auth = null;

    /**
     * If storage is enabled, this will hold the storage provider object.
     *
     * @var \snuze\Persistence\Interfaces\StorageProviderInterface
     */
    private $storage = null;

    /**
     * Whether or not to automatically sleep() if the Reddit API rate limit has
     * been exceeded. When true, Snuze will pause execution until the rate limit
     * is reset. When false, Snuze will fail with an exception.
     *
     * @var bool
     */
    private $autosleep = true;

    /**
     * Constructor. An AuthenticationState must be supplied. Optionally, a
     * storage provider object that implements StorageProviderInterface may
     * be supplied; if it's null or not passed, storage will be disabled.
     *
     * Instantiating a Snuze object really ought to be done via a SnuzeFactory.
     *
     * @param \snuze\AuthenticationState $auth A configured AuthenticationState object
     * @param \snuze\Persistence\Interfaces\StorageProviderInterface $storage
     *      Optional; a storage provider object that implements StorageProviderInterface.
     * @see \snuze\SnuzeFactory
     */
    public function __construct(AuthenticationState $auth,
            Persistence\Interfaces\StorageProviderInterface $storage = null) {

        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();

        /* Set local properties */
        $this->auth = $auth;
        $this->storage = $storage;

        $this->debug('The narwhal bacons at midnight');
    }

    /**
     * Retrieve a new access token from the Reddit API server. The token will
     * be associated with whichever Reddit account is running Snuze at the time
     * the request is made.
     *
     * @return \snuze\Reddit\AccessToken
     */
    public function fetchAccessToken(): \snuze\Reddit\AccessToken {

        $this->info('Requesting access token for ' . $this->auth->username);

        /* Build and send a request */
        $request = new Request\AccessToken();
        $request->addParameter(Request\AccessToken::PARAM_GRANT_TYPE, 'password')
                ->addParameter(Request\AccessToken::PARAM_USERNAME,
                        $this->auth->username)
                ->addParameter(Request\AccessToken::PARAM_PASSWORD,
                        $this->auth->password);
        $json = $this->sendRequest($request);

        /* Test for a common error condition */
        if (preg_match('|invalid_grant|mi', $json)) {
            throw new \snuze\Exception\AuthenticationException($this,
                    'Authentication failed. Verify the username, password, '
                    . "client ID, and secret you've configured; and ensure "
                    . "the user '{$this->auth->username}' has been added as a "
                    . 'developer in the Reddit preferences for your app.');
        }

        return (new Reddit\AccessToken($this->auth->username))->fromJson($json);
    }

    /**
     * Retrieve the properties of one or more Thing objects from the API server.
     *
     * Things of different kinds can be requested at the same time, but only
     * comments, links, and subreddits (t1, t3, and t5 kinds) may be requested
     * via this method.
     *
     * @param array $thingFullnames An array containing one or more Thing
     *      fullnames, e.g. ['t1_abc', 't3_def', 't5_ghi'], about which to
     *      request properties.
     * @return \snuze\Reddit\Listing\Listing A generic Listing object
     *      containing Thing descendant objects. The Listing is iterable. If you
     *      request multiple Thing types, use instanceof or get_class() to
     *      determine each Thing's subtype.
     * @see https://www.reddit.com/dev/api/#GET_api_info
     */
    public function fetchInfo(array $thingFullnames): \snuze\Reddit\Listing\Listing {

        if (empty($thingFullnames)) {
            $this->warning('fetchInfo received a request for no objects; '
                    . 'returning an empty Listing');
            return new Reddit\Listing\Listing();
        }

        $things = join(',', $thingFullnames);
        $this->info('Requesting info for thing(s): ' . $things);

        /* Build and send a request */
        $request = new Request\Info();
        $request->addParameter(Request\Info::PARAM_ID, $things);
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\Listing)->fromJson($json);
    }

    /**
     * Retrieve a single link's properties from the API server.
     *
     * @param string $linkFullname The target link's fullname e.g. "t3_cnrvpk"
     * @return \snuze\Reddit\Thing\Link A Link object populated
     *      with information about the target link
     */
    public function fetchLink(string $linkFullname): \snuze\Reddit\Thing\Link {

        $this->info('Requesting link: ' . $linkFullname);

        $links = $this->fetchInfo([$linkFullname]);
        foreach ($links as $link) {
            /* There should only be one, but this is the fastest way to get it */
            return $link;
        }
        return null;
    }

    /**
     * Retrieve a subreddit's "Controversial" links listing from the API server.
     *
     * All arguments other than $subredditName are optional.
     *
     * Only one of $after or $before may be set; if either argument is passed,
     * the other must be null (or not passed).
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 25
     * @param string $after Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results after, akin to "next page"
     * @param string $before Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results before, akin to "previous page"
     * @param int $count Optional. When paging through a subreddit, set this to
     *      the number of links that have already been retrieved. Use in
     *      combination with $after or $before.
     * @param bool $showAll Optional. If true, disregards any "hide links..."
     *      preferences enabled for the currently authenticated user
     * @param string $period Optional. One of 'hour', 'day', 'week', 'month',
     *      'year', or 'all', to define which time period this request is
     *      constrained to. If not set, defaults to 'day'
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     */
    public function fetchLinksControversial(string $subredditName,
            int $limit = 25, string $after = null, string $before = null,
            int $count = null, bool $showAll = false, string $period = 'day'): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting controversial links ({$period}) for /r/{$subredditName}");
        $this->debug(var_export(func_get_args(), true));

        /* Build and send a request */
        $request = new Request\Links\ControversialLinks($subredditName);
        $request->addParameter(Request\Links\Links::PARAM_LIMIT, $limit);
        $request->addParameter(Request\Links\Links::PARAM_AFTER, $after);
        $request->addParameter(Request\Links\Links::PARAM_BEFORE, $before);
        $request->addParameter(Request\Links\Links::PARAM_COUNT, $count);
        if ($showAll === true) {
            $request->addParameter(Request\Links\Links::PARAM_SHOW, 'all');
        }
        $request->addParameter(Request\Links\ControversialLinks::PARAM_TIME,
                $period);
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\LinkListing())->fromJson($json);
    }

    /**
     * Retrieve a subreddit's "Hot" links listing from the API server.
     *
     * All arguments other than $subredditName are optional.
     *
     * Only one of $after or $before may be set; if either argument is passed,
     * the other must be null (or not passed).
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 25
     * @param string $after Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results after, akin to "next page"
     * @param string $before Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results before, akin to "previous page"
     * @param int $count Optional. When paging through a subreddit, set this to
     *      the number of links that have already been retrieved. Use in
     *      combination with $after or $before.
     * @param bool $showAll Optional. If true, disregards any "hide links..."
     *      preferences enabled for the currently authenticated user
     * @param string $locality Optional. A locality code for which to request
     *      a geographically-oriented "hot" listing. Valid locality codes can
     *      be found in the Request\Links\HotLinks class, or at the URL below.
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     * @see \snuze\Request\Links\HotLinks for valid locality codes
     * @see https://www.reddit.com/dev/api/#GET_hot for valid locality codes
     */
    public function fetchLinksHot(string $subredditName, int $limit = 25,
            string $after = null, string $before = null, int $count = null,
            bool $showAll = false, string $locality = null): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting hot links for /r/{$subredditName}");
        $this->debug(var_export(func_get_args(), true));

        /* Build and send a request */
        $request = new Request\Links\HotLinks($subredditName);
        $request->addParameter(Request\Links\Links::PARAM_LIMIT, $limit);
        $request->addParameter(Request\Links\Links::PARAM_AFTER, $after);
        $request->addParameter(Request\Links\Links::PARAM_BEFORE, $before);
        $request->addParameter(Request\Links\Links::PARAM_COUNT, $count);
        if ($showAll === true) {
            $request->addParameter(Request\Links\Links::PARAM_SHOW, 'all');
        }
        if (!empty($locality)) {
            $request->addParameter(Request\Links\HotLinks::PARAM_GEO, $locality);
        }
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\LinkListing())->fromJson($json);
    }

    /**
     * Retrieve a subreddit's "New" links listing from the API server.
     *
     * All arguments other than $subredditName are optional.
     *
     * Only one of $after or $before may be set; if either argument is passed,
     * the other must be null (or not passed).
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 25
     * @param string $after Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results after, akin to "next page"
     * @param string $before Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results before, akin to "previous page"
     * @param int $count Optional. When paging through a subreddit, set this to
     *      the number of links that have already been retrieved. Use in
     *      combination with $after or $before.
     * @param bool $showAll Optional. If true, disregards any "hide links..."
     *      preferences enabled for the currently authenticated user
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     */
    public function fetchLinksNew(string $subredditName, int $limit = 25,
            string $after = null, string $before = null, int $count = null,
            bool $showAll = false): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting new links for /r/{$subredditName}");
        $this->debug(var_export(func_get_args(), true));

        /* Build and send a request */
        $request = new Request\Links\NewLinks($subredditName);
        $request->addParameter(Request\Links\Links::PARAM_LIMIT, $limit);
        $request->addParameter(Request\Links\Links::PARAM_AFTER, $after);
        $request->addParameter(Request\Links\Links::PARAM_BEFORE, $before);
        $request->addParameter(Request\Links\Links::PARAM_COUNT, $count);
        if ($showAll === true) {
            $request->addParameter(Request\Links\Links::PARAM_SHOW, 'all');
        }
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\LinkListing())->fromJson($json);
    }

    /**
     * Retrieve random link(s) for a subreddit from the API server.
     *
     * By default, only one link is retrieved (it's still returned inside of
     * a LinkListing object). If $limit is specified, and its value is greater
     * than 1, a separate API request will be made for each link, with each one
     * counting against the rate limit.
     *
     * This method doesn't work on subreddits with the "allow this subreddit to
     * be exposed to users in /r/all, /r/popular, default, and trending lists"
     * preference disabled.
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 1.
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     */
    public function fetchLinksRandom(string $subredditName, int $limit = 1): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting random link(s) for /r/{$subredditName}");

        /* Build a request, only one is required regardless of $limit */
        $request = new Request\Links\RandomLinks($subredditName);

        /* Create a LinkListing to hold the results */
        $listing = new Reddit\Listing\LinkListing();

        for ($i = 0; $i < $limit; $i++) {
            /*
             * Reddit doesn't return a link listing here; instead, it redirects
             * to the public permalink of this link, in JSON format. A specific
             * element of this JSON needs to be passed to Link's fromJson().
             */
            $responseArray = json_decode($this->sendRequest($request), true);
            if (empty($responseArray[0]['data']['children'][0])) {
                /* No links found in the response */
                return $listing;
            }
            /* Build a Link object */
            $link = (new Reddit\Thing\Link())->fromJson(
                    json_encode($responseArray[0]['data']['children'][0],
                            JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
                    )
            );
            $listing->add($link);
        }

        return $listing;
    }

    /**
     * Retrieve a subreddit's "Rising" links listing from the API server.
     *
     * All arguments other than $subredditName are optional.
     *
     * Only one of $after or $before may be set; if either argument is passed,
     * the other must be null (or not passed).
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 25
     * @param string $after Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results after, akin to "next page"
     * @param string $before Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results before, akin to "previous page"
     * @param int $count Optional. When paging through a subreddit, set this to
     *      the number of links that have already been retrieved. Use in
     *      combination with $after or $before.
     * @param bool $showAll Optional. If true, disregards any "hide links..."
     *      preferences enabled for the currently authenticated user
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     */
    public function fetchLinksRising(string $subredditName, int $limit = 25,
            string $after = null, string $before = null, int $count = null,
            bool $showAll = false): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting rising links for /r/{$subredditName}");
        $this->debug(var_export(func_get_args(), true));

        /* Build and send a request */
        $request = new Request\Links\RisingLinks($subredditName);
        $request->addParameter(Request\Links\Links::PARAM_LIMIT, $limit);
        $request->addParameter(Request\Links\Links::PARAM_AFTER, $after);
        $request->addParameter(Request\Links\Links::PARAM_BEFORE, $before);
        $request->addParameter(Request\Links\Links::PARAM_COUNT, $count);
        if ($showAll === true) {
            $request->addParameter(Request\Links\Links::PARAM_SHOW, 'all');
        }
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\LinkListing())->fromJson($json);
    }

    /**
     * Retrieve a subreddit's "Top" links listing from the API server.
     *
     * All arguments other than $subredditName are optional.
     *
     * Only one of $after or $before may be set; if either argument is passed,
     * the other must be null (or not passed).
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @param int $limit Optional. The maximum number of links to retrieve.
     *      Values from 1 to 100 are acceptable; if not set, defaults to 25
     * @param string $after Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results after, akin to "next page"
     * @param string $before Optional. The fullname e.g. "t3_cevca2" of a link
     *      to retrieve results before, akin to "previous page"
     * @param int $count Optional. When paging through a subreddit, set this to
     *      the number of links that have already been retrieved. Use in
     *      combination with $after or $before.
     * @param bool $showAll Optional. If true, disregards any "hide links..."
     *      preferences enabled for the currently authenticated user
     * @param string $period Optional. One of 'hour', 'day', 'week', 'month',
     *      'year', or 'all', to define which time period this request is
     *      constrained to. If not set, defaults to 'day'
     * @return \snuze\Reddit\Listing\LinkListing A LinkListing object
     *      containing a collection of Link objects, each one representing a
     *      single Reddit link. The LinkListing is iterable.
     */
    public function fetchLinksTop(string $subredditName, int $limit = 25,
            string $after = null, string $before = null, int $count = null,
            bool $showAll = false, string $period = 'day'): \snuze\Reddit\Listing\LinkListing {

        $this->info("Requesting top links ({$period}) for /r/{$subredditName}");
        $this->debug(var_export(func_get_args(), true));

        /* Build and send a request */
        $request = new Request\Links\TopLinks($subredditName);
        $request->addParameter(Request\Links\Links::PARAM_LIMIT, $limit);
        $request->addParameter(Request\Links\Links::PARAM_AFTER, $after);
        $request->addParameter(Request\Links\Links::PARAM_BEFORE, $before);
        $request->addParameter(Request\Links\Links::PARAM_COUNT, $count);
        if ($showAll === true) {
            $request->addParameter(Request\Links\Links::PARAM_SHOW, 'all');
        }
        $request->addParameter(Request\Links\TopLinks::PARAM_TIME, $period);
        $json = $this->sendRequest($request);

        return (new Reddit\Listing\LinkListing())->fromJson($json);
    }

    /**
     * Retrieve the currently authenticated user's account information from the
     * API server. The data will correspond to whichever Reddit account is
     * running Snuze at the time the request is made.
     *
     * @return \snuze\Reddit\Thing\Account\MyAccount A MyAccount
     *      object populated with the Snuze user's account information
     */
    public function fetchMyAccount(): \snuze\Reddit\Thing\Account\MyAccount {

        $this->info("Requesting my own ({$this->auth->username}) account data");

        $json = $this->sendRequest(new Request\Me());
        return (new Reddit\Thing\Account\MyAccount())->fromJson($json);
    }

    /**
     * Retrieve a subreddit's main properties from the API server.
     *
     * @param string $subredditName The target subreddit's name e.g. "funny"
     * @return \snuze\Reddit\Thing\Subreddit A Subreddit object
     *      populated with information about the target subreddit
     */
    public function fetchSubreddit(string $subredditName): \snuze\Reddit\Thing\Subreddit {

        $this->info('Requesting subreddit: ' . $subredditName);

        $json = $this->sendRequest(new Request\SubredditAbout($subredditName));
        return (new Reddit\Thing\Subreddit())->fromJson($json);
    }

    /**
     * Retrieve a Reddit user's public account information from the API server.
     *
     * @param string $username The username of the target account
     * @return \snuze\Reddit\Thing\Account\UserAccount A UserAccount object
     *      populated with information about the target account
     */
    public function fetchUser(string $username): \snuze\Reddit\Thing\Account\UserAccount {

        $this->info('Requesting account: ' . $username);

        $json = $this->sendRequest(new Request\UserAbout($username));
        return (new Reddit\Thing\Account\UserAccount())->fromJson($json);
    }

    /**
     * Check that the current authentication state is valid. If not, an access
     * token is sought, first from storage if that's enabled, and then from
     * the API server.
     *
     * @return bool True if authentication succeeded, else an exception is thrown
     * @throws \snuze\Exception\AuthenticationException
     */
    private function authenticate(): bool {

        /* If we're already authenticated, there's nothing to do */
        if ($this->auth->token->isValid()) {
            return true;
        }

        /* Look for an unexpired token for this user in storage */
        if (!empty($this->storage)) {
            $token = $this->storage->getMapper('AccessToken')->retrieveOneFor($this->auth->username);
            if (!empty($token) && $token->isValid()) {
                $this->auth->token = $token;
                $this->info('Authenticated user '
                        . "{$this->auth->username} via stored token");
                return true;
            }
        }

        /* Request a token from the API server */
        $token = $this->fetchAccessToken();
        if (!empty($token) && $token->isValid()) {
            if (!empty($this->storage)) {
                /* Save the token */
                $this->storage->getMapper('AccessToken')->persist($token);
                /* While we're here, remove any expired tokens from storage */
                $this->storage->getMapper('AccessToken')->purge();
            }
            $this->auth->token = $token;
            $this->info('Authenticated user '
                    . "{$this->auth->username} via API request");
            return true;
        }

        /* ¯\_(ツ)_/¯ */
        throw new \snuze\Exception\AuthenticationException($this,
                "Unable to authenticate user {$this->auth->username} "
                . 'with Reddit.');
    }

    /**
     * Dispatch a RequestSender to send a request to the Reddit API server.
     *
     * @param \snuze\Request\Request $request A Request subtype object
     *      to send to the Reddit API server
     * @return string A JSON response from the Reddit API server
     * @throws \snuze\Exception\AuthenticationException
     * @throws \snuze\Exception\RateLimitException
     */
    private function sendRequest(Request\Request $request): string {

        /* Ensure we have an access token if this request type requires it */
        if ($request->getRequiresAuthentication() === true && !$this->authenticate()) {
            throw new \snuze\Exception\AuthenticationException($this,
                    'Request type ' . get_class($request) . ' requires '
                    . "authentication, but a token couldn't be obtained");
        }

        /* If we're rate limited, we can't make this request (yet) */
        if ($this->auth->bucket->isEmpty()) {
            if ($this->autosleep) {
                $seconds = $this->auth->bucket->getRefillTime() - time();
                $seconds++; /* ...just to be sure */
                $this->warning("Rate limited; sleeping {$seconds} sec until refill");
                sleep($seconds);
            }
            else {
                throw new \snuze\Exception\RateLimitException($this,
                        'The rate limit has been exceeded and autosleep is '
                        . 'disabled; unable to continue.');
            }
        }

        /* Decrement the RateLimitBucket prior to sending the request. */
        $this->auth->bucket->drip();

        /* Dispatch a new RequestSender to submit the request */
        return (new RequestSender($request, $this->auth))->send();
    }

    /**
     * Get a Mapper object from the storage provider to facilitate persisting
     * and retrieving objects to and from storage. Pass the type of object you
     * want a Mapper for: 'Subreddit', 'Link', 'Comment', etc. The public
     * convenience methods that wrap this one should be used instead of calling
     * this directly.
     *
     * @param string $objectClass The type of object a Mapper is needed for
     * @return \snuze\Persistence\Interfaces\MapperInterface
     * @throws \snuze\Exception\PersistenceException
     */
    private function getStorageMapper(string $objectClass): \snuze\Persistence\Interfaces\MapperInterface {
        if (empty($this->storage)) {
            throw new \snuze\Exception\PersistenceException($this,
                    'Storage is not enabled. This feature is unavailable.');
        }
        return $this->storage->getMapper($objectClass);
    }

    /**
     * Get a LinkMapper from the storage provider to facilitate persisting
     * and retrieving links.
     *
     * @return \snuze\Persistence\Interfaces\LinkMapperInterface
     */
    public function getLinkMapper(): \snuze\Persistence\Interfaces\LinkMapperInterface {
        return $this->getStorageMapper('Link');
    }

    /**
     * Get a SubredditMapper from the storage provider to facilitate persisting
     * and retrieving subreddits.
     *
     * @return \snuze\Persistence\Interfaces\SubredditMapperInterface
     */
    public function getSubredditMapper(): \snuze\Persistence\Interfaces\SubredditMapperInterface {
        return $this->getStorageMapper('Subreddit');
    }

}
