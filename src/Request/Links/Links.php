<?php

declare(strict_types=1);

namespace snuze\Request\Links;

/**
 * The Links class represents the common properties of all requests for links
 * (AKA posts or submissions) from a subreddit. Parameters that can be used
 * by all Links subtypes are defined here. Several subtypes of Links exist to
 * define the properties specific to those request types.
 *
 * You can't instantiate a generic Links request; instead, you should create
 * and use the various subtypes: HotLinks, NewLinks, RisingLinks, etc.
 */
abstract class Links extends \snuze\Request\Request
{

    /**
     * Defines the "controversial" sort order
     */
    const SORT_CONTRO = 'controversial';

    /**
     * Defines the "hot" sort order
     */
    const SORT_HOT = 'hot';

    /**
     * Defines the "new" sort order
     */
    const SORT_NEW = 'new';

    /**
     * Defines the "random" sort order
     */
    const SORT_RANDOM = 'random';

    /**
     * Defines the "rising" sort order
     */
    const SORT_RISING = 'rising';

    /**
     * Defines the "top" sort order
     */
    const SORT_TOP = 'top';

    /**
     * Defines the "after" parameter as valid. Used to control pagination. If
     * set, this should be a link's fullname e.g. "t3_ce4vtr"
     */
    const PARAM_AFTER = 'after';

    /**
     * Defines the "before" parameter as valid. Used to control pagination. If
     * set, this should be a link's fullname e.g. "t3_ce4vtr"
     */
    const PARAM_BEFORE = 'before';

    /**
     * Defines the "count" parameter as valid. If set, this should be the
     * number of links that have already been seen in this listing.
     */
    const PARAM_COUNT = 'count';

    /**
     * Defines the "include_categories" parameter as valid
     */
    const PARAM_INCLUDE_CATEGORIES = 'include_categories';

    /**
     * Defines the "limit" parameter as valid
     */
    const PARAM_LIMIT = 'limit';

    /**
     * Defines the "show" parameter as valid. The only acceptable value for
     * this parameter is 'all'. When set, any filters the authenticated user
     * has set ("don't show me submissions..." preferences) will be disabled
     * for this listing.
     */
    const PARAM_SHOW = 'show';

    /**
     * Defines the "sr_detail" parameter as valid
     */
    const PARAM_SR_DETAIL = 'sr_detail';

    /**
     * A string that indicates which sort order this listing is using; for
     * example, 'new' to designate /r/subreddit/new/. Corresponds to the SORT_
     * constants defined above.
     *
     * @var string
     */
    private $_sort = null;

    /**
     * Constructor. Calls parent, then sets properties specific to this Request.
     */
    public function __construct(string $subredditName, string $sort) {

        /* All Request children must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Validate the subreddit name so we don't send a bogus request */
        if (!preg_match(\snuze\Reddit\Thing\Subreddit::REGEX_VALID_NAME,
                        $subredditName)) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid subreddit name');
        }

        /* Set HTTP verb */
        $this->setVerb(self::VERB_GET);

        /* Set sort order */
        $this->setSort($sort);

        /* Set endpoint path */
        $this->setEndpointPath("/r/{$subredditName}/{$sort}");
    }

    private function setSort(string $sort) {
        if (!in_array($sort, $this->getValidSorts())) {
            throw new \snuze\Exception\ArgumentException($this,
                    "Unrecognized sort order '{$sort}'");
        }
        $this->_sort = $sort;
        return $this;
    }

    /**
     * Overrides and calls parent. Performs parameter validation specific to
     * this type of Request.
     *
     * @return bool Whether or not the Request is ready to send to the API
     * @throws \snuze\Exception\ArgumentException
     */
    public function validate(): bool {

        parent::validate();

        /*
         * PARAM_LIMIT must be between 0 and 100, inclusive. If 0 is supplied,
         * the Reddit API server will default to 25.
         */
        if ($this->getParameter(self::PARAM_LIMIT) < 0 ||
                $this->getParameter(self::PARAM_LIMIT) > 100) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Out-of-bounds value for parameter ' . self::PARAM_LIMIT);
        }

        /* Check the value of PARAM_SHOW. 'all' is the only valid value */
        if (($this->getParameter(self::PARAM_SHOW) ?? 'all') !== 'all') {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid value for parameter ' . self::PARAM_SHOW);
        }

        /* Only one of PARAM_AFTER or PARAM_BEFORE may be set */
        if (!empty($this->getParameter(self::PARAM_AFTER)) &&
                !empty($this->getParameter(self::PARAM_BEFORE))) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Only one of ' . self::PARAM_AFTER . ' or '
                    . self::PARAM_BEFORE . ' may be set at once');
        }

        return true;
    }

    /**
     * Get an array of supported sort orders.
     *
     * @return array Links SORT_ constants and their values
     */
    public static function getValidSorts(): array {
        return array_filter((new \ReflectionClass(__CLASS__))->getConstants(),
                function($value, $key) {
            return (strpos($key, 'SORT_') === 0);
        }, ARRAY_FILTER_USE_BOTH);
    }

}
