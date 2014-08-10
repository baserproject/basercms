<?php
/**
 * CakeRequest Test
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('CakeRequest', 'Network');

/**
 * CakeRequest class
 * 
 * @package Baser.Test.Case.Network
 */
class CakeRequestTest extends CakeTestCase {
	
	public function testNormalizedHere() {
		
		$CakeRequest = new CakeRequest('/');
		Router::setRequestInfo($CakeRequest);
		$params = Router::parse($CakeRequest->url);
		$CakeRequest->addParams($params);
		$this->assertEquals('/index', $CakeRequest->normalizedHere());
		
		// TODO Fixture の準備が必要
		// TODO 固定ページのテストも必要
		$CakeRequest = new CakeRequest('/news/index');
		Router::setRequestInfo($CakeRequest);
		$params = Router::parse($CakeRequest->url);
		$CakeRequest->addParams($params);
		$this->assertEquals('/news/index', $CakeRequest->normalizedHere());
		
		$CakeRequest = new CakeRequest('/news/');
		Router::setRequestInfo($CakeRequest);
		$params = Router::parse($CakeRequest->url);
		$CakeRequest->addParams($params);
		$this->assertEquals('/news/index', $CakeRequest->normalizedHere());

		$CakeRequest = new CakeRequest('/news');
		Router::setRequestInfo($CakeRequest);
		$params = Router::parse($CakeRequest->url);
		$CakeRequest->addParams($params);
		$this->assertEquals('/news/index', $CakeRequest->normalizedHere());

	}
}