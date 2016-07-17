<?php
/**
 * A simple OAuth client class for CakePHP.
 *
 * Uses the OAuth library from http://oauth.googlecode.com/svn/code/php/
 *
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 */

require('OAuth.php');
App::uses('HttpSocket', 'Network/Http');
App::uses('CakeText', 'Utility');

class OAuthClient {
    private $url = null;
    private $consumerKey = null;
    private $consumerSecret = null;
    private $fullResponse = null;

    public function __construct($consumerKey, $consumerSecret = '') {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Call API with a GET request. Returns either false on failure or an HttpResponse object.
     */
    public function get($accessTokenKey, $accessTokenSecret, $url, array $getData = array()) {
        $accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
        $request = $this->createRequest('GET', $url, $accessToken, $getData);

        return $this->doGet($request->to_url());
    }

    public function getAccessToken($accessTokenURL, $requestToken, $httpMethod = 'POST', array $parameters = array()) {
        $this->url = $accessTokenURL;
        $queryStringParams = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);
        $parameters['oauth_verifier'] = $queryStringParams['oauth_verifier'];
        $request = $this->createRequest($httpMethod, $accessTokenURL, $requestToken, $parameters);

        return $this->getToken($request);
    }

    /**
     * Returns an HttpResponse object for the previous request, or null, if there was no request.
     */
    public function getFullResponse() {
        return $this->fullResponse;
    }

    /**
     * @param $requestTokenURL
     * @param $callback An absolute URL to which the server will redirect the resource owner back when the Resource Owner
     *                  Authorization step is completed. If the client is unable to receive callbacks or a callback URL 
     *                  has been established via other means, the parameter value MUST be set to oob (case sensitive), to 
     *                  indicate an out-of-band configuration. Section 2.1 from http://tools.ietf.org/html/rfc5849
     * @param $httpMethod 'POST' or 'GET'
     * @param $parameters
     */
    public function getRequestToken($requestTokenURL, $callback = 'oob', $httpMethod = 'POST', array $parameters = array()) {
        $this->url = $requestTokenURL;
        $parameters['oauth_callback'] = $callback;
        $request = $this->createRequest($httpMethod, $requestTokenURL, null, $parameters);

        return $this->getToken($request);
    }

    /**
     * Call API with a POST request. Returns either false on failure or an HttpResponse object.
     */
    public function post($accessTokenKey, $accessTokenSecret, $url, array $postData = array()) {
        $accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
        $request = $this->createRequest('POST', $url, $accessToken, $postData);

        return $this->doPost($url, $request->to_postdata());
    }

    /**
     * Call API with a POST request, the content type set to multipart/form-data.
     * This is, for example, necessary for Twitter's update_with_media API method (https://dev.twitter.com/docs/api/1/post/statuses/update_with_media)
     * $paths a key-value array, example: array('media[]' => '/home/dho/avatar.png')
     * Returns either false on failure or an HttpResponse object.
     */
    public function postMultipartFormData($accessTokenKey, $accessTokenSecret, $url, array $paths, array $postData = array()) {
        $accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
        $request = $this->createRequest('POST', $url, $accessToken, array());
        $authorization = str_replace('Authorization: ', '', $request->to_header());

        return $this->doPostMultipartFormData($url, $authorization, $paths, $postData);
    }

    protected function createOAuthToken(array $response) {
        if (isset($response['oauth_token']) && isset($response['oauth_token_secret'])) {
            return new OAuthToken($response['oauth_token'], $response['oauth_token_secret']);
        }

        return null;
    }

    private function createConsumer() {
        return new OAuthConsumer($this->consumerKey, $this->consumerSecret);
    }

    private function createRequest($httpMethod, $url, $token, array $parameters) {
        $consumer = $this->createConsumer();
        $request = OAuthRequest::from_consumer_and_token($consumer, $token, $httpMethod, $url, $parameters);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);

        return $request;
    }

    private function doGet($url) {
        $socket = new HttpSocket();
        $result = $socket->get($url);
        $this->fullResponse = $result;

        return $result;
    }

    private function doPost($url, $data) {
        $socket = new HttpSocket();
        $result = $socket->post($url, $data);
        $this->fullResponse = $result;

        return $result;
    }

    private function doPostMultipartFormData($url, $authorization, $paths, $data) {
        App::uses('String', 'Utility');
        $boundary = CakeText::uuid();

        $body = "--{$boundary}\r\n";

        foreach ($data as $key => $value) {
            $body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n";
            $body .= "\r\n";
            $body .= "{$value}\r\n";
            $body .= "--{$boundary}\r\n";
        }

        foreach ($paths as $key => $path) {
            $body .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$path}\"\r\n";
            $body .= "\r\n";
            $body .= file_get_contents($path) . "\r\n";
            $body .= "--{$boundary}--\r\n";
        }

        $socket = new HttpSocket();
        $result = $socket->request(array('method' => 'POST',
                                         'uri' => $url,
                                         'header' => array(
                                             'Authorization' => $authorization,
                                             'Content-Type' => "multipart/form-data; boundary={$boundary}"),
                                         'body' => $body));
        $this->fullResponse = $result;

        return $result;
    }

    private function getToken($request) {
        if ($request->get_normalized_http_method() == 'POST') {
            $data = $this->doPost($this->url, $request->to_postdata());
        } else {
            $data = $this->doGet($request->to_url());
        }

        $response = array();
        parse_str($data->body, $response);

        return $this->createOAuthToken($response);
    }
}
