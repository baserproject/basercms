<?php

/**
 * test for FeedDetail
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS FeedDetails Community <http://sites.google.com/site/baserFeedDetails/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS FeedDetails Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('FeedDetail', 'Feed.Model');

class FeedDetailTest extends BaserTestCase {

	public $fixtures = array(
		'plugin.feed.Default/FeedConfig',
		'baser.Default.FeedDetail',
	);

	public function setUp() {
		$this->FeedDetail = ClassRegistry::init('Feed.FeedDetail');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->FeedDetail);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test必須チェック() {
		$this->FeedDetail->create(array(
			'FeedDetail' => array()
		));

		$this->assertFalse($this->FeedDetail->validates());

		$this->assertArrayHasKey('name', $this->FeedDetail->validationErrors);
		$this->assertEquals('フィード詳細名を入力してください。', current($this->FeedDetail->validationErrors['name']));

		$this->assertArrayHasKey('url', $this->FeedDetail->validationErrors);
		$this->assertEquals('フィードURLを入力してください。', current($this->FeedDetail->validationErrors['url']));
	}

	public function test桁数チェック異常系() {
		$this->FeedDetail->create(array(
			'FeedDetail' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'url' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'category_filter' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456'
			)
		));
		$this->assertFalse($this->FeedDetail->validates());

		$this->assertArrayHasKey('name', $this->FeedDetail->validationErrors);
		$this->assertEquals('フィード詳細名は50文字以内で入力してください。', current($this->FeedDetail->validationErrors['name']));

		$this->assertArrayHasKey('url', $this->FeedDetail->validationErrors);
		$this->assertEquals('フィードURLは255文字以内で入力してください。', current($this->FeedDetail->validationErrors['url']));

		$this->assertArrayHasKey('category_filter', $this->FeedDetail->validationErrors);
		$this->assertEquals('カテゴリフィルタは255文字以内で入力してください。', current($this->FeedDetail->validationErrors['category_filter']));
	}

	public function test桁数チェック正常系() {
		$this->FeedDetail->create(array(
			'FeedDetail' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'url' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'category_filter' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345'
			)
		));

		$this->assertTrue($this->FeedDetail->validates());
	}

/*
 * 初期値を取得する
 */
	public function testGetDefaultValue() {
		$result = $this->FeedDetail->getDefaultValue(1);
		$expected = array('FeedDetail' => array(
				'feed_config_id' => 1,
				'name' => 'baserCMSニュース',
				'cache_time' => '+30 minutes'
			)
		);
	}

}
