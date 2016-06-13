<?php

/**
 * test for FeedModel
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('Feed', 'Feed.Model');

class FeedTest extends BaserTestCase {

	public $fixtures = array(
		'plugin.feed.Default/FeedConfig',
		'baser.Default.FeedDetail',
	);

	public function setUp() {
		$this->Feed = ClassRegistry::init('Feed.Feed');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->Feed);
		parent::tearDown();
	}

/**
 * フィードを取得する
 * 
 * cacheExpiresのテストはcache()メソッドを使用しているので、実装していない
 */
	public function testGetFeed() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$url = FULL_BASE_URL . "/news/index.rss";
		
		$result = $this->Feed->getFeed($url);
		$this->assertEquals($result['Channel']['title']['value'], '新着情報｜baserCMS inc. [デモ]', 'RSSフィードを正しく取得できません');

		$result = $this->Feed->getFeed($url, 1);
		$this->assertEquals(count($result['Items']), 1, '指定した件数分のRSSを取得できません');
		
		$result = $this->Feed->getFeed($url, 2);
		$this->assertEquals(count($result['Items']), 2, '指定した件数分のRSSを取得できません');

		$result = $this->Feed->getFeed($url, 10, null, 'hoge');
		$this->assertEquals(count($result['Items']), 0, '指定したカテゴリのRSSを取得できません');

		$result = $this->Feed->getFeed($url, 10, null, 'プレスリリース');
		$this->assertEquals(count($result['Items']), 2, '指定したカテゴリのRSSを取得できません');
	}


/**
 * URL文字列に対しキャッシュファイルのハッシュを生成して返す
 */
	public function testCreateCacheHash() {
		$result =	$this->Feed->createCacheHash('', '/');
		$this->assertEquals($result, '2c0187b8225c556ddea9e68e268f2bd3', 'ハッシュが正しくありません');
		
		$result =	$this->Feed->createCacheHash('.php', '/test');
		$this->assertEquals($result, 'c6978d994b82654df04fd1cea5451e43.php', 'ハッシュが正しくありません');
	}

/**
 * 文字列から<img>タグとURLを抽出する
 *
 * @param string $string 元の文字列
 * @param array $img <img>タグとURLの配列の期待値
 * @return void
 *
 * @dataProvider extractImgDataProvider
 */
	public function testExtractImg($string, $img) {
		$Feed = ClassRegistry::init('Feed.Feed');
		$this->assertEquals($img, $Feed->extractImg($string));
	}

/**
 * extractImg用のデータプロバイダ
 *
 * @return array
 */
	public function extractImgDataProvider() {
		return array(
			array(
				'<p><a href="http://example.com" target="_blank"><img align="" alt="osc2014_fukuoka_logo.png" src="http://basercms.net/files/uploads/osc2014_fukuoka_logo.png"></a></p><p>baserCMSユーザー会の',
				array(
					'tag' => '<img align="" alt="osc2014_fukuoka_logo.png" src="http://basercms.net/files/uploads/osc2014_fukuoka_logo.png">',
					'url' => 'http://basercms.net/files/uploads/osc2014_fukuoka_logo.png'
				)
			),
			array(
				'<p><a href="http://example.com/" rel="colorbox" target="_blank" title="logo.jpg"><img align="" alt="logo.jpg" src = "http://basercms.net/files/uploads/logo.jpg" /></a></p>',
				array(
					'tag' => '<img align="" alt="logo.jpg" src = "http://basercms.net/files/uploads/logo.jpg" />',
					'url' => 'http://basercms.net/files/uploads/logo.jpg'
				)
			),
			array(
				'<p>imgタグがない場合</p>',
				array(
					'tag' => ''
				)
			)
		);
	}

}
