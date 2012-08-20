<?php
/**
 * A simple OAuth consumer for CakePHP.
 *
 * Requires the OAuth library from http://oauth.googlecode.com/svn/code/php/
 *
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP4対応版（アルゴリズムはsha1のみ対応）
 */

require('OAuth.php');
App::import('Core', 'http_socket');

// using an underscore in the class name to avoid a naming conflict with the OAuth library
class OAuth_Consumer {
	/**
	 * @access	private
	 */
	var $url = null;
	/**
	 * @access	private
	 */
	var $consumerKey = null;
	/**
	 * @access	private
	 */
	var $consumerSecret = null;
	/**
	 * @access	private
	 */
	var $fullResponse = null;

	function OAuth_Consumer($consumerKey, $consumerSecret = '') {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * Call API with a GET request
	 * @access	public
	 */
	function get($accessTokenKey, $accessTokenSecret, $url, $getData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('GET', $url, $accessToken, $getData);

		return $this->doGet($request->to_url());
	}
	/**
	 *  @access	public
	 */
	function getAccessToken($accessTokenURL, &$requestToken, $httpMethod = 'POST', $parameters = array()) {
		$this->url = $accessTokenURL;
		$queryStringParams = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);
		$parameters['oauth_verifier'] = $queryStringParams['oauth_verifier'];
		$request = $this->createRequest($httpMethod, $accessTokenURL, $requestToken, $parameters);

		return $this->doRequest($request);
	}

	/**
	 * Useful for debugging purposes to see what is returned when requesting a request/access token.
	 * @access	public
	 */
	function getFullResponse() {
		return $this->fullResponse;
	}

	/**
	 * @param $requestTokenURL
	 * @param $callback An absolute URL to which the Service Provider will redirect the User back when the Obtaining User
	 * 					Authorization step is completed. If the Consumer is unable to receive callbacks or a callback URL
	 * 					has been established via other means, the parameter value MUST be set to oob (case sensitive), to
	 * 					indicate an out-of-band configuration. Section 6.1.1 from http://oauth.net/core/1.0a
	 * @param $httpMethod 'POST' or 'GET'
	 * @param $parameters
	 * @access	public
	 */
	function getRequestToken($requestTokenURL, $callback = 'oob', $httpMethod = 'POST', $parameters = array()) {
		$this->url = $requestTokenURL;
		$parameters['oauth_callback'] = $callback;
		$request = $this->createRequest($httpMethod, $requestTokenURL, null, $parameters);

		return $this->doRequest($request);
	}

	/**
	 * Call API with a POST request
	 * @access	public
	 */
	function post($accessTokenKey, $accessTokenSecret, $url, $postData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('POST', $url, $accessToken, $postData);

		return $this->doPost($url, $request->to_postdata());
	}
	/**
	 * @access	protected
	 */
	function createOAuthToken($response) {
		
		if (isset($response['oauth_token']) && isset($response['oauth_token_secret'])) {
			$OAuthToken = new OAuthToken($response['oauth_token'], $response['oauth_token_secret']);
			return $OAuthToken;
		}

		return null;
	}
	/**
	 * @access	private
	 */
	function &createConsumer() {
		$OAuthConsumer = new OAuthConsumer($this->consumerKey, $this->consumerSecret);
		return $OAuthConsumer;
	}
	/**
	 * @access	private
	 */
	function &createRequest($httpMethod, $url, $token, $parameters) {
		$consumer =& $this->createConsumer();
		$request =& OAuthRequest::from_consumer_and_token($consumer, $token, $httpMethod, $url, $parameters);
		$sha1 =& new OAuthSignatureMethod_HMAC_SHA1();
		$request->sign_request($sha1, $consumer, $token);
		return $request;
	}
	/**
	 * @access	private
	 */
	function doGet($url) {
		$socket = new HttpSocket();
		return $socket->get($url);
	}
	/**
	 * @access	private
	 */
	function doPost($url, $data) {
		$socket = new HttpSocket();
		return $socket->post($url, $data);
	}
	/**
	 * @access	private
	 */
	function doRequest($request) {

		if ($request->get_normalized_http_method() == 'POST') {
			$data = $this->doPost($this->url, $request->to_postdata());
		} else {
			$data = $this->doGet($request->to_url());
		}

		$this->fullResponse = $data;
		$response = array();
		parse_str($data, $response);

		return $this->createOAuthToken($response);
	}
}