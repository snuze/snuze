<?php

declare(strict_types=1);

namespace snuze;

/**
 * The RateLimitBucket represents the state of Reddit's API rate limit mechanism.
 *
 * Reddit divides each hour into six ten-minute slices, and allows authenticated
 * users to issue a limited number of API requests during each slice. When your
 * allotment for the current time slice has been exhausted, no more requests are
 * permitted until the next time slice begins. The rate limit resets every ten
 * minutes (:00, :10, etc.), refilling the bucket.
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
class RateLimitBucket extends SnuzeObject
{

    /**
     * How many requests the bucket holds when it's full. The default is 600.
     */
    const BUCKET_VOLUME = 600;

    /**
     * The number of requests remaining during this ten-minute period
     *
     * @var int
     */
    private $remaining = self::BUCKET_VOLUME;

    /**
     * The unix epoch time at which the bucket will next be refilled
     *
     * @var int
     */
    private $refillTime = 0;

    /**
     * Constructor.
     */
    public function __construct() {

        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));
    }

    /**
     * Get the number of requests remaining during this ten-minute period
     *
     * @return int
     */
    public function getRemaining(): int {
        return $this->remaining;
    }

    /**
     * Get the unix epoch time at which the bucket will next be refilled
     *
     * @return int
     */
    public function getRefillTime(): int {
        return $this->refillTime;
    }

    /**
     * Get whether or not the bucket is currently empty
     *
     * @return bool
     */
    public function isEmpty(): bool {
        return $this->remaining <= 0 && $this->refillTime > time();
    }

    /**
     * Decrement the value of $remaining by one.
     *
     * @return void
     */
    public function drip(): void {
        $this->remaining -= 1;
    }

    /**
     * Update the bucket state
     *
     * @param int $remaining The number of requests remaining during this
     *      ten-minute period
     * @param int $secondsUntilRefill The number of seconds until the bucket
     *      will be refilled
     */
    public function update(int $remaining, int $secondsUntilRefill) {
        $this->debug("Updating RateLimitBucket with remaining: {$remaining}, "
                . "secondsUntilRefill: {$secondsUntilRefill}");
        $this->remaining = $remaining;
        $this->refillTime = time() + $secondsUntilRefill;
    }

}
