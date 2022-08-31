<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Lib
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcUtil', 'Lib');
App::uses('BcAuthComponent', 'Controller/Component');

/**
 * Class BcUtilTest
 *
 * @package Baser.Test.Case.Lib
 * @property BcUtil $BcUtil
 */
class BcUtilTest extends BaserTestCase
{
	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.SiteConfig',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User'
	];

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		// 前のテストで変更されている為
		$BcAuth = new BcAuthComponent(new ComponentCollection([]));
		$BcAuth->setSessionKey('Auth.User');
		parent::setUp();
	}

	public function tearDown()
	{
		session_unset();
		parent::tearDown();
	}


	/**
	 * 管理システムかチェック
	 *
	 * @param string $url 対象URL
	 * @param bool $expect 期待値
	 * @dataProvider isAdminSystemDataProvider
	 */
	public function testIsAdminSystem($url, $expect)
	{
		$this->_getRequest($url);
		$result = BcUtil::isAdminSystem();
		$this->assertEquals($expect, $result, '正しく管理システムかチェックできません');
	}

	/**
	 * isAdminSystem用データプロバイダ
	 *
	 * @return array
	 */
	public function isAdminSystemDataProvider()
	{
		return [
			['admin', true],
			['admin/hoge', true],
			['/admin/hoge', true],
			['admin/', true],
			['hoge', false],
			['hoge/', false],
		];
	}

	/**
	 * 管理ユーザーかチェック
	 *
	 * @param string $userGroupId ユーザーグループ名
	 * @param bool $expect 期待値
	 * @dataProvider isAdminUserDataProvider
	 */
	public function testIsAdminUser($userGroupId, $expect)
	{
		$Session = new CakeSession();
		$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
		$Session->write('Auth.' . $sessionKey . '.UserGroup.id', $userGroupId);
		$result = BcUtil::isAdminUser();
		$this->assertEquals($expect, $result, '正しく管理ユーザーがチェックできません');
	}

	/**
	 * isAdminUser用データプロバイダ
	 *
	 * @return array
	 */
	public function isAdminUserDataProvider()
	{
		return [
			[Configure::read('BcApp.adminGroupId'), true],
			['hoge', false],
			['', false],
		];
	}

	/**
	 * ログインユーザーのデータを取得する
	 */
	public function testLoginUser()
	{
		// ログインしていない場合
		$result = BcUtil::loginUser();
		$this->assertNull($result, 'ログインユーザーのデータを正しく取得できません');

		// ログインしている場合
		$Session = new CakeSession();
		$Session->write('Auth.' . BcUtil::authSessionKey() . '.name', 'admin');
		$result = BcUtil::loginUser();
		$this->assertEquals($result['name'], 'admin', 'ログインユーザーのデータを正しく取得できません');
	}

	/**
	 * 現在ログインしているユーザーのユーザーグループ情報を取得する
	 */
	public function testLoginUserGroup()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ログインしているユーザー名を取得
	 */
	public function testLoginUserName()
	{
		// ログインしていない場合
		$result = BcUtil::loginUserName();
		$this->assertEmpty($result, 'ログインユーザーのデータを正しく取得できません');

		// ログインしている場合
		$Session = new CakeSession();
		$Session->write('Auth.' . BcUtil::authSessionKey() . '.name', 'hoge');
		$result = BcUtil::loginUserName();
		$this->assertEquals('hoge', $result, 'ログインユーザーのデータを正しく取得できません');
	}

	/**
	 * 認証用のキーを取得
	 */
	public function testAuthSessionKey()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ログインしているユーザーのセッションキーを取得
	 */
	public function testGetLoginUserSessionKey()
	{
		// セッションキーを未設定の場合
		$result = BcUtil::getLoginUserSessionKey();
		$this->assertEquals('User', $result, 'セッションキーを取得を正しく取得できません');

		// セッションキーを設定した場合
		BcAuthComponent::$sessionKey = 'Auth.Hoge';
		$result = BcUtil::getLoginUserSessionKey();
		$this->assertEquals($result, 'Hoge', 'セッションキーを取得を正しく取得できません');
	}


	/**
	 * テーマ梱包プラグインのリストを取得する
	 */
	public function testGetThemesPlugins()
	{
		$theme = Configure::read('BcSite.theme');
		$path = BASER_THEMES . $theme . DS . 'Plugin';

		// ダミーのプラグインディレクトリを削除
		$Folder = new Folder();
		$Folder->delete($path);

		// プラグインが存在しない場合
		$result = BcUtil::getThemesPlugins($theme);
		$expect = [];
		$this->assertEquals($expect, $result, 'テーマ梱包プラグインのリストを正しく取得できません');

		// プラグインが存在する場合
		// ダミーのプラグインディレクトリを作成
		$Folder->create($path . DS . 'dummy1');
		$Folder->create($path . DS . 'dummy2');

		$result = BcUtil::getThemesPlugins($theme);
		// ダミーのプラグインディレクトリを削除
		$Folder->delete($path);

		$expect = ['dummy1', 'dummy2'];
		$this->assertEquals($expect, $result, 'テーマ梱包プラグインのリストを正しく取得できません');
	}

	/**
	 * 現在適用しているテーマ梱包プラグインのリストを取得する
	 */
	public function testGetCurrentThemesPlugins()
	{
		$theme = Configure::read('BcSite.theme');
		$path = BASER_THEMES . $theme . DS . 'Plugin';
		$Folder = new Folder();
		$Folder->delete($path);
		$this->assertEquals([], BcUtil::getCurrentThemesPlugins(), '現在適用しているテーマ梱包プラグインのリストを正しく取得できません。');
	}

	/**
	 * スキーマ情報のパスを取得する
	 */
	public function testGetSchemaPath()
	{
		// Core
		$result = BcUtil::getSchemaPath();
		$this->assertEquals(BASER_CONFIGS . 'Schema', $result, 'Coreのスキーマ情報のパスを正しく取得できません');

		// Blog
		$result = BcUtil::getSchemaPath('Blog');
		$this->assertEquals(BASER_PLUGINS . 'Blog/Config/Schema', $result, 'プラグインのスキーマ情報のパスを正しく取得できません');
	}

	/**
	 * 初期データのパスを取得する
	 *
	 * 初期データのフォルダは アンダースコア区切り推奨
	 *
	 * @param string $plugin プラグイン名
	 * @param string $theme テーマ名
	 * @param string $pattern 初期データの類型
	 * @param string $expect 期待値
	 * @dataProvider getDefaultDataPathDataProvider
	 */
	public function testGetDefaultDataPath($plugin, $theme, $pattern, $expect)
	{
		$isset_ptt = isset($pattern) && isset($theme);
		$isset_plt = isset($plugin) && isset($theme);
		$isset_plptt = isset($plugin) && isset($pattern) && isset($theme);
		$Folder = new Folder();

		// 初期データ用のダミーディレクトリを作成
		if ($isset_ptt) {
			$Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern);
		}
		if ($isset_plt && !$isset_plptt) {
			$Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin);
		}
		if ($isset_plptt) {
			$Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin);
		}

		$result = BcUtil::getDefaultDataPath($plugin, $theme, $pattern);

		// 初期データ用のダミーディレクトリを削除
		if ($isset_ptt) {
			$Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern);
		}
		if ($isset_plt && !$isset_plptt) {
			$Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin);
		}
		if ($isset_plptt) {
			$Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin);
		}

		$this->assertEquals($expect, $result, '初期データのパスを正しく取得できません');
	}

	/**
	 * getDefaultDataPath用データプロバイダ
	 *
	 * @return array
	 */
	public function getDefaultDataPathDataProvider()
	{
		return [
			[null, null, null, BASER_CONFIGS . 'data/default'],
			[null, 'nada-icons', null, BASER_THEMES . 'nada-icons/Config/data/default'],
			[null, 'nada-icons', 'not_default', BASER_THEMES . 'nada-icons/Config/data/not_default'],
			['Blog', null, null, BASER_PLUGINS . 'Blog/Config/data/default'],
			['Blog', 'nada-icons', null, BASER_THEMES . 'nada-icons/Config/data/default/Blog'],
			['Blog', 'nada-icons', 'not_default', BASER_THEMES . 'nada-icons/Config/data/not_default/Blog'],
		];
	}

	/**
	 * シリアライズ / アンシリアライズ
	 */
	public function testSerialize()
	{
		ini_set('display_errors', "Off");
		$orig = PHPUnit_Framework_Error_Notice::$enabled;
		PHPUnit_Framework_Error_Notice::$enabled = false;

		// BcUtil::serialize()でシリアライズした場合
		$serialized = BcUtil::serialize('hoge');
		$result = BcUtil::unserialize($serialized);
		$this->assertEquals('hoge', $result, 'BcUtil::serialize()で正しくシリアライズ/アンシリアライズできません');

		// serialize()のみでシリアライズした場合
		$serialized = serialize('hoge');
		$result = BcUtil::unserialize($serialized);
		$this->assertEquals('hoge', $result, 'serializeのみで正しくシリアライズ/アンシリアライズできません');

		PHPUnit_Framework_Error_Notice::$enabled = $orig;
		ini_set('display_errors', "On");

	}

	/**
	 * アンシリアライズ
	 * base64_decode が前提
	 */
	public function testUnserialize()
	{
		$this->markTestIncomplete('testSerializeにて実装');
	}

	/**
	 * URL用に文字列を変換する
	 *
	 * できるだけ可読性を高める為、不要な記号は除外する
	 */
	public function testUrlencode()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * レイアウトテンプレートのリストを取得する
	 */
	public function testGetTemplateList()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 全てのテーマリストを取得する
	 */
	public function testGetAllThemeList()
	{
		$themes = BcUtil::getAllThemeList();
		$this->assertTrue(in_array('nada-icons', $themes));
		$this->assertTrue(in_array('admin-third', $themes));
	}

	/**
	 * テーマリストを取得する
	 */
	public function testGetThemeList()
	{
		$themes = BcUtil::getThemeList();
		$this->assertTrue(in_array('nada-icons', $themes));
		$this->assertFalse(in_array('admin-third', $themes));
	}

	/**
	 * 管理画面用のテーマリストを取得する
	 */
	public function testGetAdminThemeList()
	{
		$themes = BcUtil::getAdminThemeList();
		$this->assertFalse(in_array('nada-icons', $themes));
		$this->assertTrue(array_key_exists('admin-third', $themes));
	}

	/**
	 * 指定したURLのドメインを取得する
	 */
	public function testGetDomain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * メインとなるドメインを取得する
	 */
	public function testGetMainDomain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 管理画面用のプレフィックスを取得する
	 */
	public function testGetAdminPrefix()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * 現在のドメインを取得する
	 */
	public function testGetCurrentDomain()
	{
		$this->assertEmpty(BcUtil::getCurrentDomain(), '$_SERVER[HTTP_HOST] の値が間違っています。');
		Configure::write('BcEnv.host', 'hoge');
		$this->assertEquals('hoge', BcUtil::getCurrentDomain(), 'ホストを変更できません。');
	}

	/**
	 * サブドメインを取得する
	 *
	 * @param string $host
	 * @param string $currentHost
	 * @param string $expects
	 * @dataProvider getSubDomainDataProvider
	 */
	public function testGetSubDomain($host, $currentHost, $expects, $message)
	{
		Configure::write('BcEnv.mainDomain', 'localhost');
		if ($currentHost) {
			Configure::write('BcEnv.host', $currentHost);
		} else {
			Configure::write('BcEnv.host', '');
		}
		$this->assertEquals($expects, BcUtil::getSubDomain($host), $message);
	}

	public function getSubDomainDataProvider()
	{
		return [
			['', '', '', '現在のサブドメイン名が不正です。'],
			['', 'hoge.localhost', 'hoge', '現在のサブドメイン名が取得できません。'],
			['', 'test.localhost', 'test', '現在のサブドメイン名が取得できません。'],
			['hoge.localhost', '', 'hoge', '引数を指定してサブドメイン名が取得できません。'],
			['test.localhost', '', 'test', '引数を指定してサブドメイン名が取得できません。'],
			['localhost', '', '', '引数を指定してサブドメイン名が取得できません。'],
		];
	}

}
