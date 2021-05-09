<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.Test.Case.Lib
 * @since            baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
App::uses('BcSite', 'Lib');

/**
 * Class BcSiteTest
 *
 * @package Baser.Test.Case.Lib
 */
class BcSiteTest extends BaserTestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Site',
		'baser.Default.Content',
		'baser.Default.User',
		'baser.Default.SiteConfig'
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
	}


	/**
	 * コンストラクタ
	 */
	public function test__construct()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * URLからサブサイトを取得する
	 */
	public function testFindCurrent()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 現在のサイトに関連するメインサイトを取得
	 */
	public function testFindCurrentMain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 現在のサイトとユーザーエージェントに関連するサブサイトを取得する
	 */
	public function testFindCurrentSub()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 関連するサブサイトを全て取得する
	 */
	public function testFindAll()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * findById
	 */
	public function testFindById()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * エイリアスからサイトを検索する
	 */
	public function testFindByAlias()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * findByUrl
	 */
	public function testFindByUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 設定が有効かどうかを判定
	 */
	public function testIsEnabled()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @param array $query クエリパラメータの配列
	 * @return void
	 * @dataProvider shouldRedirectsDataProvider
	 */
	public function testShouldRedirects($expect, $url, array $query = null)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$request = new CakeRequest($url, false);
		$request->query = $query;
		$this->assertEquals($expect, $this->agent->shouldRedirects($request));
	}

	public function shouldRedirectsDataProvider()
	{
		return [
			[false, '/s/'],
			[false, '/s/news/index'],
			[false, '/s/service', ['smartphone' => 'on']],
			[true, '/'],
			[true, '/news/index'],
			[true, '/service'],
			[false, '/news/index', ['smartphone' => 'off']],
			[true, '/m/'],
			[true, '/m/service/index'],
			[false, '/m/service/index', ['smartphone' => 'off']]
		];
	}

	/**
	 * URLが存在するか確認
	 */
	public function testExistsUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * エイリアスを反映したURLを生成
	 * 同一URL設定のみ利用可
	 * @dataProvider makeUrlDataProvider
	 */
	public function testMakeUrl($alias, $url, $expected)
	{
		$request = $this->_getRequest($url);
		$site = BcSite::findByAlias($alias);
		$url = $site->makeUrl($request);
		$this->assertEquals($expected, $url);
	}

	public function makeUrlDataProvider()
	{
		return [
			['', '/', '/'],
			['', '/index', '/'],
			['', '/about', '/about'],
			['s', '/', '/s/'],
			['s', '/index', '/s/'],
			['s', '/about', '/s/about'],
		];
	}

	/**
	 * メインサイトを取得
	 */
	public function testGetMain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * エイリアスを除外したURLを取得
	 */
	public function testGetPureUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 初期状態に戻す
	 */
	public function testFlash()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ホストを取得する
	 */
	public function testGetHost()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
