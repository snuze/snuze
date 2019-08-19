<?php

declare(strict_types=1);

namespace snuze;

/**
 * A RequestSender is responsible for transmitting a request to Reddit's
 * API server and returning the response.
 *
 * If an error is encountered, an exception will be thrown. It's important
 * that your application uses some error handling strategy to deal with
 * these exceptions. Some of them, like transient connection issues or
 * server HTTP 500 errors, happen fairly often.
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
class RequestSender extends SnuzeObject
{

    /**
     * A Request subtype object defining the request to be sent
     *
     * @var \snuze\Request\Request
     */
    private $request = null;

    /**
     * An AuthenticationState object
     *
     * @var \snuze\AuthenticationState
     */
    private $auth = null;

    /**
     * Constructor.
     *
     * @param \snuze\Request\Request $request A Request subtype object
     *      that's been populated and is ready to submit to the API server
     * @param \snuze\AuthenticationState $auth An AuthenticationState
     *      object containing the credentials needed to talk to the API server
     */
    public function __construct(\snuze\Request\Request $request,
            \snuze\AuthenticationState $auth) {

        /* All SnuzeObject subtypes must call parent ctor */
        parent::__construct();
        $this->debug('ctor args: ' . var_export(func_get_args(), true));

        /* Set local properties */
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Validate the request and send it to the Reddit API server.
     *
     * @return string A JSON response from the Reddit API server
     * @throws \snuze\Exception\RuntimeException
     * @throws \snuze\Exception\AuthenticationException
     * @throws \snuze\Exception\ForbiddenException
     * @throws \snuze\Exception\NotFoundException
     * @throws \snuze\Exception\ServerErrorException
     */
    public function send(): string {

        /* Alias for convenience */
        $request = &$this->request;

        /* Check that the request is well-formed */
        $request->validate();

        /* Build the target URI */
        $uri = $request->getApiUri() . $request->getEndpointPath();
        if ($request->getVerb() === Request\Request::VERB_GET) {
            /* Append a query string if necessary */
            $uri .= '?' . http_build_query($request->getParameters());
        }

        /* Configure curl */
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->auth->userAgent);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getVerb());
        curl_setopt($ch, CURLOPT_USERPWD,
                $this->auth->clientId . ':' . $this->auth->clientSecret);

        /* If this is a POST request, set the POST fields */
        if ($request->getVerb() === Request\Request::VERB_POST) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getParameters());
        }

        /* If we're authenticated, add a header with the access token */
        if ($this->auth->token->isValid()) {
            $request->addHeader(
                    'Authorization', 'bearer ' . $this->auth->token->getToken()
            );
        }

        /* If there are any headers, assemble them for curl */
        if (!empty($request->getHeaders())) {
            $headers = [];
            foreach ($request->getHeaders() as $key => $val) {
                $headers[] = "{$key}: {$val}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        /*
         * Set a callback fn to store the server's response headers. They're
         * used to determine the current rate limit state.
         */
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                function($ch, $header) use (&$responseHeaders) {
            $trimHeader = trim($header);
            if (!empty($trimHeader) && strpos($trimHeader, ':') !== false) {
                list ($name, $value) = explode(':', $trimHeader, 2);
                $responseHeaders[trim($name)] = trim($value);
            }
            return strlen($header);
        });

        /* Send the request to the API server */
        $this->debug('Calling curl_exec() for ' . $request->_ident());
        $response = curl_exec($ch);

        /*
         * Bail if curl encountered an error. This typically indicates a
         * network problem or connection timeout; you can test for the string
         * "timed out" in the exception message.
         */
        $curlError = curl_error($ch);
        if ($response === false || !empty($curlError)) {
            throw new \snuze\Exception\RuntimeException($this,
                    "Got curl error for {$request->_ident()}: " . $curlError);
        }

        /* Get the HTTP response code */
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        /* Log the request, and the response code/headers/data */
        $this->debug("Sent request for {$request->_ident()}: "
                . var_export(curl_getinfo($ch, CURLINFO_HEADER_OUT), true));
        $this->debug("Got response code for {$request->_ident()}: " . $httpCode);
        $this->debug("Got response headers for {$request->_ident()}: "
                . var_export($responseHeaders, true));
        $this->debug("Got response data for {$request->_ident()}: " . $response);

        /* Raise an exception if the HTTP response code wasn't 200 */
        if ($httpCode !== 200) {
            if ($httpCode === 401) {
                throw new \snuze\Exception\AuthenticationException($this,
                        'Request failed; unauthorized. Possible password or token '
                        . "problem. Code: {$httpCode}, Response: {$response}");
            }
            else if ($httpCode === 403) {
                throw new \snuze\Exception\ForbiddenException($this,
                        "Request failed; forbidden. You can't access that object. "
                        . "Code: {$httpCode}, Response: {$response}");
            }
            else if ($httpCode === 404) {
                throw new \snuze\Exception\NotFoundException($this,
                        "Request failed; requested object was not found. "
                        . "Code: {$httpCode}, Response: {$response}");
            }
            else if ($httpCode === 500) {
                throw new \snuze\Exception\ServerErrorException($this,
                        "Request failed; Reddit API server error. "
                        . "Code: {$httpCode}, Response: {$response}");
            }
            else {
                /* Could be anything; use generic RuntimeException */
                throw new \snuze\Exception\RuntimeException($this,
                        "Request failed. Code: {$httpCode}, Response: {$response}");
            }
        }

        /* Update the rate limit state */
        if (!empty($responseHeaders['x-ratelimit-remaining']) &&
                !empty($responseHeaders['x-ratelimit-reset'])) {
            $this->auth->bucket->update(
                    (int) $responseHeaders['x-ratelimit-remaining'],
                    (int) $responseHeaders['x-ratelimit-reset']
            );
        }

        /* Make sure the response data is valid JSON */
        if (json_decode($response) === null) {
            throw new \snuze\Exception\RuntimeException($this,
                    'Server response was not valid JSON. Contents follow: '
                    . $response);
        }

        /* Return the JSON response data */
        return $response;
    }

}
