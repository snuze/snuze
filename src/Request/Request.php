<?php

declare(strict_types=1);

namespace snuze\Request;

/**
 * Request represents the common features of any request that will be sent to
 * the Reddit API. The target URI, HTTP verb, and any headers or parameters
 * (query string or POST fields) are all stored in this object.
 *
 * You can't instantiate a generic Request; instead, you should create and
 * use the various subtypes.
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
abstract class Request extends \snuze\SnuzeObject
{

    /**
     * The base URIs to which API requests are sent. Requests to obtain an
     * authorization token must be made to the "unauthenticated" URI. Most
     * other requests are generally made to the "authenticated" URI.
     *
     * Don't edit these values unless you have a phenomenally good reason.
     */
    const API_URIS = [
        'unauthenticated' => "https://www.reddit.com",
        'authenticated'   => "https://oauth.reddit.com",
    ];

    /**
     * Define the HTTP GET verb as valid
     */
    const VERB_GET = 'GET';

    /**
     * Define the HTTP POST verb as valid
     */
    const VERB_POST = 'POST';

    /**
     * Define the HTTP DELETE verb as valid
     */
    const VERB_DELETE = 'DELETE';

    /**
     * The base URI to which this request should be sent. Most requests will
     * use the authenticated URI, so it's the default. Child class ctors are
     * responsible for setting a different URI when needed.
     *
     * @var string
     */
    private $apiUri = self::API_URIS['authenticated'];

    /**
     * The specific API endpoint path for this action. Gets appended to $apiUri.
     * Child class ctors are responsible for setting this value.
     *
     * @var string
     */
    private $endpointPath = null;

    /**
     * The HTTP verb (e.g. GET, POST) used for this request type. Must be set
     * to one of the VERB_ constants defined above; defaults to self::VERB_GET
     *
     * @var string
     */
    private $verb = self::VERB_GET;

    /**
     * Whether or not this type of request requires authentication.
     *
     * @var bool
     */
    private $requiresAuthentication = true;

    /**
     * An array of extra HTTP headers, if any, that should be sent with this
     * request. Each element's key is the header name, and its value is the
     * header value.
     *
     * Use addHeader('name', 'value') to populate the array.
     *
     * @var string[]
     */
    private $headers = [];

    /**
     * An array of parameters to send with this request. For GET requests,
     * these will be built into a query string and appended to the URI. For
     * POST requests, these will be supplied as the POST data. Each element's
     * key is the parameter name, and its value is the parameter value.
     *
     * Use addParameter('name', 'value') to populate the array.
     *
     * @var string[]
     */
    protected $parameters = [];

    /**
     * An array of the names of parameters that must be set in order for this
     * Request to be considered valid. Children of Request should set this in
     * their ctors.
     *
     * @var string[]
     */
    protected $mandatoryParameters = [];

    /**
     * Add an HTTP header to this request.
     *
     * @param string $name The name of the header, e.g. 'Connection'
     * @param string $value The value of the header, e.g. 'Close'
     * @return Request This object
     */
    public function addHeader(string $name, string $value): Request {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Get the array of HTTP headers configured for this Request.
     *
     * @return array An array of HTTP header key/value pairs
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Add a parameter to the query string or POST data for this Request.
     *
     * If the parameter already exists, it will be overwritten with the new
     * value. If $value is empty, the parameter will be unset.
     *
     * @param string $name The name of the parameter, e.g. 'link_fullname'
     * @param scalar|null $value The value of the parameter, e.g. 't3_chungus'
     * @return Request This object
     * @throws \snuze\Exception\ArgumentException
     */
    public function addParameter(string $name, $value = null): Request {
        if (!in_array($name, $this->getValidParameters())) {
            throw new \snuze\Exception\ArgumentException($this,
                    "Unrecognized parameter for this Request type: '{$name}'");
        }

        /* If an empty $value was passed, unset the parameter */
        if (empty($value)) {
            unset($this->parameters[$name]);
            return $this;
        }

        /* Parameter values must be scalar */
        if (!is_scalar($value)) {
            throw new \snuze\Exception\ArgumentException($this,
                    "Parameter value must be a scalar type: '{$name}'");
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Get the value of a single parameter, by name; or null if it's not set
     *
     * @param string $name The name of the parameter to retrieve
     * @return scalar|null Whatever value has been set for this parameter
     */
    public function getParameter(string $name) {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Get the array of parameters configured for this Request.
     *
     * @return array An array of parameter key/value pairs
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * Get the API base URI for this type of Request
     *
     * @return string The API base URI
     */
    public function getApiUri(): string {
        return $this->apiUri;
    }

    /**
     * Set the base URI to which this request will be sent.
     *
     * @param string $apiUri The base URI for this type of Request
     * @return Request This object
     * @throws \snuze\Exception\ArgumentException
     */
    protected function setApiUri(string $apiUri) {

        /* Test for a valid URI */
        if (!filter_var($apiUri, FILTER_VALIDATE_URL)) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid URI, try again');
        }

        $this->apiUri = $apiUri;
        return $this;
    }

    /**
     * Get the API endpoint path for this type of Request
     *
     * @return string The API endpoint path e.g. "/api/foo/bar"
     */
    public function getEndpointPath(): string {
        return $this->endpointPath;
    }

    /**
     * Set the API endpoint path to which this request will be posted.
     *
     * @param string $endpointPath The API endpoint path e.g. "/api/foo/bar"
     * @return Request This object
     * @throws \snuze\Exception\ArgumentException
     */
    protected function setEndpointPath(string $endpointPath) {

        /* Test the path value */
        if (strpos($endpointPath, '/') !== 0) {
            throw new \snuze\Exception\ArgumentException($this,
                    'Invalid path; should be an absolute path '
                    . 'beginning with a forward slash');
        }

        $this->endpointPath = $endpointPath;
        return $this;
    }

    /**
     * Get the HTTP verb configured for this Request type
     *
     * @return string The HTTP verb e.g. "GET" or "DELETE"
     */
    public function getVerb(): string {
        return $this->verb;
    }

    /**
     * Set the HTTP verb that will be used for this Request type
     *
     * @param string $verb One of the self::VERB_ constants
     * @return $this
     * @throws \snuze\Exception\ArgumentException
     */
    protected function setVerb(string $verb) {

        /* Test the verb value */
        if (!in_array($verb, $this->getValidVerbs())) {
            throw new \snuze\Exception\ArgumentException($this,
                    "Unrecognized HTTP verb '{$verb}'");
        }

        $this->verb = $verb;
        return $this;
    }

    /**
     * Get whether or not this type of Request requires authentication.
     *
     * @return bool Whether or not this Request type requires authentication
     */
    public function getRequiresAuthentication(): bool {
        return $this->requiresAuthentication;
    }

    /**
     * Set whether or not this type of Request requires authentication.
     *
     * @param bool $requiresAuthentication Whether this Request type requires
     *      authentication
     * @return Request This object
     */
    protected function setRequiresAuthentication(bool $requiresAuthentication) {
        $this->requiresAuthentication = $requiresAuthentication;
        return $this;
    }

    /**
     * Get an array containing the list of parameter names that must be
     * defined when sending a Request of this type.
     *
     * @return array The parameter names that are required for this Request type
     */
    public function getMandatoryParameters(): array {
        return static::$mandatoryParameters;
    }

    /**
     * Returns true if the mandatory properties for this request type are all
     * set. Child classes of Request should override this method, performing
     * any additional checks specific to their Request type.
     *
     * @return bool Whether or not the Request is ready to send to the API
     * @throws \snuze\Exception\RuntimeException
     */
    public function validate(): bool {
        $this->debug('Checking request sanity');

        /* Make sure an HTTP verb is set */
        if (empty($this->verb)) {
            throw new \snuze\Exception\RuntimeException($this,
                    'Unset $verb property; call setVerb() from the ctor');
        }

        /* Make sure an API endpoint path is set */
        if (empty($this->endpointPath)) {
            throw new \snuze\Exception\RuntimeException($this,
                    'Unset $endpointPath property; must be set in ctor');
        }

        /* Check that all mandatory fields are populated */
        $missing = array_diff($this->mandatoryParameters,
                array_keys($this->parameters));
        if (!empty($missing)) {
            throw new \snuze\Exception\RuntimeException($this,
                    'Missing mandatory parameter(s) must be set before sending: '
                    . join(', ', $missing));
        }

        return true;
    }

    /**
     * Get an array of valid outgoing parameter names for this Request type.
     *
     * @return array The Request child object's PARAM_ constants and their values
     */
    public static function getValidParameters(): array {
        return array_filter((new \ReflectionClass(static::class))->getConstants(),
                function($value, $key) {
            return (strpos($key, 'PARAM_') === 0);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get an array of supported HTTP verbs.
     *
     * @return array Request's VERB_ constants and their values
     */
    public static function getValidVerbs(): array {
        return array_filter((new \ReflectionClass(__CLASS__))->getConstants(),
                function($value, $key) {
            return (strpos($key, 'VERB_') === 0);
        }, ARRAY_FILTER_USE_BOTH);
    }

}
