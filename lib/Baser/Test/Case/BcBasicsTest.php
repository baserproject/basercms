<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

/**
 * test for basics.php
 *
 * @package Baser.Test.Case
 */
class BcBasicsTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.BlogContent',
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.SiteConfig',
		'baser.Default.Site',
		'baser.Default.Content',
	];

	public function setUp()
	{
		parent::setUp();
		BcSite::flash();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * WebサイトのベースとなるURLを取得する
	 * TODO BC_DEPLOY_PATTERNで分岐した場合のテストの追加
	 *
	 * @param string $script App.baseUrlの値
	 * @param string $script $_SERVER['SCRIPT_FILENAME']の値
	 * @param string $expect 期待値
	 * @dataProvider baseUrlDataProvider
	 */
	public function testBaseUrl($baseUrl, $expect)
	{
		// 初期化
		Configure::write('App.baseUrl', $baseUrl);
		if (isConsole()) {
			$_SERVER['SCRIPT_FILENAME'] = APP . 'Console' . DS . 'cake.php';
			$_SERVER['SCRIPT_NAME'] = APP . 'Console' . DS . 'cake.php';
		}
		$result = baseUrl();
		$this->assertEquals($expect, $result, 'WebサイトのベースとなるURLを正しく取得できません');

	}

	public function baseUrlDataProvider()
	{
		return [
			['/hoge/test', '/hoge/test/'],
			[null, '/'],
			['/hoge/test', '/hoge/test/'],
			[null, '/'],
		];
	}


	/**
	 * ドキュメントルートを取得する
	 */
	public function testDocRoot()
	{
		$_SERVER['SCRIPT_FILENAME'] = WWW_ROOT . 'test.php';

		if (isConsole()) {
			$expected = str_replace('app' . DS . 'Console' . DS . 'cake.php', '', $_SERVER['SCRIPT_NAME']);

		} else {
			$path = explode('/', $_SERVER['SCRIPT_NAME']);
			krsort($path);
			$expected = $_SERVER['SCRIPT_FILENAME'];
			foreach($path as $value) {
				$reg = "/\/" . $value . "$/";
				$expected = preg_replace($reg, '', $expected);
			}
		}
		$result = docRoot();
		$this->assertEquals($expected, $result);
	}

	/**
	 * リビジョンを取得する
	 */
	public function testRevision()
	{
		$version = 'baserCMS 3.0.6.1 beta';
		$result = revision($version);
		$this->assertEquals('1', $result, '正しくリビジョンを取得できません');
	}

	/**
	 * バージョンを特定する一意の数値を取得する
	 */
	public function testVerpoint()
	{
		$version = 'baserCMS 3.0.6.1';
		$result = verpoint($version);
		$this->assertEquals(3000006001, $result, '正しくバージョンを特定する一意の数値を取得できません');

		$version = 'baserCMS 3.0.6.1 beta';
		$result = verpoint($version);
		$this->assertEquals(false, $result, '正しくバージョンを特定する一意の数値を取得できません');
	}

	/**
	 * 拡張子を取得する
	 *
	 * @param string $content mimeタイプ
	 * @param string $fileName ファイル名
	 * @param string $expect 期待値
	 * @dataProvider decodeContentDataProvider
	 */
	public function testDecodeContent($content, $fileName, $expect)
	{
		$result = decodeContent($content, $fileName);
		$this->assertEquals($expect, $result, '正しく拡張子を取得できません');
	}

	public function decodeContentDataProvider()
	{
		return [
			['image/jpeg', null, 'jpg'],
			[null, 'hoge.png', 'png'],
			[null, 'hoge', false],
			[null, null, false],
		];
	}

	/**
	 * 環境変数よりURLパラメータを取得する
	 *
	 * @param string $agentAlias BcRequest.agentAliasの値
	 * @param string $url URL
	 * @param string $expect 期待値
	 * @param string $message テスト失敗時に表示するメッセージ
	 * @dataProvider getUrlParamFromEnvDataProvider
	 */
	public function testGetUrlParamFromEnv($agentAlias, $url, $expect, $message = null)
	{
		Configure::write('BcRequest.agentAlias', $agentAlias);
		$_GET['url'] = $url;
		$result = getUrlParamFromEnv();
		$this->assertEquals($expect, $result, $message);
	}

	public function getUrlParamFromEnvDataProvider()
	{
		return [
			[null, '/s/test/', 's/test/', 'URLパラメータのモバイルプレフィックスを正しく除外できません']
		];
	}

	/**
	 * 環境変数よりURLを取得する
	 *
	 * @param string $url $_GET['url']の値
	 * @param string $request $_SERVER['REQUEST_URI']の値
	 * @param string $baseUrl App.BaseUrlの値
	 * @param string $expect 期待値
	 * @param string $message テスト失敗時に表示するメッセージ
	 * @dataProvider getUrlFromEnvDataProvider
	 */
	public function testGetUrlFromEnv($get, $request, $baseUrl, $expect, $message = null)
	{
		// 初期化
		$_GET['url'] = $get;
		$_SERVER['REQUEST_URI'] = $request;
		Configure::write('App.baseUrl', $baseUrl);

		$result = getUrlFromEnv();
		$this->assertEquals($expect, $result, $message);
	}

	public function getUrlFromEnvDataProvider()
	{
		return [
			['/get/', null, null, 'get/', '$_GET["url"]からURLを正しく取得できません'],
			['/get/url/test', null, null, 'get/url/test', '$_GET["url"]からURLを正しく取得できません'],
			[null, '/req/', null, 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
			[null, '/req/test.php?a=aaa&b=bbb', null, 'req/test.php', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
			[null, baseUrl() . '/req/', null, 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
			[null, '/base/req/', '/base/', 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
			[null, '/base/req/', '/base/url/', 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
		];
	}

	/**
	 * モバイルプレフィックスは除外したURLを取得する
	 */
	public function testGetPureUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Viewキャッシュを削除する
	 * TODO basics.php 295行目 $homesにバグ？あり
	 *            app/tmp/cache/views/のキャッシュファイルを複数回削除している
	 *
	 * @param string $url
	 * @param string $ext
	 * @dataProvider clearViewCacheDataProvider
	 */
	public function testClearViewCache($url, $ext)
	{
		$viewCachePath = CACHE . 'views' . DS;
		if ($url == '/' || $url == '/index' || $url == '/index.html' || $url == '/m/' || $url == '/m/index' || $url == '/m/index.html') {
			$cache = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . $ext, true);
			// 削除実行
			clearViewCache($url, $ext);

		} elseif ($url) {
			// ダミーのキャッシュファイルを生成
			$cache = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . $ext, true);
			$cacheHoge = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . '.hoge', true);
			if (preg_match('/\/index$/', $url)) {
				$replacedUrl = preg_replace('/\/index$/', '', $url);
				$replacedCache = new File($viewCachePath . DS . strtolower(Inflector::slug($replacedUrl)) . $ext, true);
			}
			// 削除実行
			clearViewCache($url, $ext);

			$this->assertTrue($cacheHoge->exists(), '指定されていない拡張子のファイルが削除されてしまいます');
			$cacheHoge->delete();
			$cacheHoge->close();

			if (preg_match('/\/index$/', $url)) {
				$this->assertFalse($replacedCache->exists(), '置換された名前のキャッシュファイルを削除できません');
				$replacedCache->delete();
				$replacedCache->close();
			}

		} else {
			// ダミーのキャッシュファイルを生成
			$cache = new File($viewCachePath . DS . 'cache', true);
			$empty = new File($viewCachePath . DS . 'empty', true);

			// 削除実行
			clearViewCache($url, $ext);

			$this->assertTrue($empty->exists(), 'emptyファイルが削除されてしまいます');
			$empty->delete();
			$empty->close();
		}

		$this->assertFalse($cache->exists(), 'キャッシュを削除できません');
		$cache->delete();
		$cache->close();

	}

	public function clearViewCacheDataProvider()
	{
		return [
			[null, null],
			['/test/', '.ext'],
			['/test/index', '.ext'],
			['/index.html', '.php'],
			['/m/index.html', '.php'],
		];
	}

	/**
	 * データキャッシュを削除する
	 */
	public function testClearDataCache()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * キャッシュファイルを全て削除する
	 */
	public function testClearAllCache()
	{
		// ダミーのキャッシュファイルを生成
		$coreConf = Cache::config('_cake_core_');
		$coreConf = $coreConf['settings'];
		$modelConf = Cache::config('_cake_model_');
		$modelConf = $modelConf['settings'];
		$envConf = Cache::config('_cake_env_');
		$envConf = $envConf['settings'];

		$coreCache = new File($coreConf['path'] . $coreConf['prefix'] . 'cache', true);
		$modelCache = new File($modelConf['path'] . $modelConf['prefix'] . 'cache', true);
		$envCache = new File($envConf['path'] . $envConf['prefix'] . 'cache', true);
		$viewCache = new File(CACHE . 'views' . DS . 'cache', true);
		$dataCache = new File(CACHE . 'datas' . DS . 'cache', true);

		// キャッシュ削除
		clearAllCache();

		$this->assertFalse($coreCache->exists());
		$this->assertFalse($modelCache->exists());
		$this->assertFalse($envCache->exists());
		$this->assertFalse($viewCache->exists());
		$this->assertFalse($dataCache->exists());

		$coreCache->close();
		$modelCache->close();
		$envCache->close();
		$viewCache->close();
		$dataCache->close();
	}

	/**
	 * baserCMSのインストールが完了しているかチェックする
	 */
	public function testIsInstalled()
	{
		$installedPath = APP . 'Config' . DS . 'install.php';

		// app/Config/installed.phpが存在しない場合
		if (rename($installedPath, $installedPath . '_copy')) {
			$result = isInstalled();
			$this->assertFalse($result, 'app/Config/installed.phpが存在していない場合にtrueが返ってきます');
		} else {
			$this->markTestIncomplete('app/Config/installed.phpのファイル名変更に失敗したのでテストをスキップしました。');
		}

		// app/Config/installed.phpが存在する場合
		if (rename($installedPath . '_copy', $installedPath)) {
			$result = isInstalled();
			$this->assertTrue($result, 'app/Config/installed.phpが存在している場合にfalseが返ってきます');
		} else {
			$this->markTestIncomplete('app/Config/installed.phpのファイル名変更に失敗したのでテストをスキップしました。');
		}

	}

	/**
	 * DBセッティングが存在するかチェックする
	 */
	public function testGetDbConfig()
	{
		$dbconfigPath = APP . 'Config' . DS . 'database.php';

		// app/Config/database.phpが存在しない場合
		if (rename($dbconfigPath, $dbconfigPath . '_copy')) {
			$result = getDbConfig();
			$this->assertFalse($result, 'app/Config/database.php が存在していない場合にtrueが返ってきます');
		} else {
			$this->markTestIncomplete('app/Config/database.php のファイル名変更に失敗したのでテストをスキップしました。');
		}

		// app/Config/database.phpが存在する場合
		if (rename($dbconfigPath . '_copy', $dbconfigPath)) {
			$result = getDbConfig();
			$this->assertContains('utf8', $result, 'app/Config/database.php が存在している場合にデータベースの情報が返ってきません');

			$result = getDbConfig('hoge');
			$this->assertFalse($result, '存在しないデータベースの設定名を入力した場合にfalseが返ってきます');

		} else {
			$this->markTestIncomplete('app/Config/database.php のファイル名変更に失敗したのでテストをスキップしました。');
		}

	}

	/**
	 * 必要な一時フォルダが存在するかチェックし、なければ生成する
	 */
	public function testCheckTmpFolders()
	{
		checkTmpFolders();

		$paths = [
			TMP . 'logs',
			TMP . 'sessions',
			TMP . 'schemas',
			TMP . 'schemas' . DS . 'core',
			TMP . 'schemas' . DS . 'plugin',
			CACHE,
			CACHE . 'models',
			CACHE . 'persistent',
			CACHE . 'views',
			CACHE . 'datas',
			CACHE . 'environment',
		];

		// フォルダが生成されているかチェック
		$result = true;
		foreach($paths as $key => $value) {
			if (!is_dir($value)) {
				$result = false;
			}
		}
		$this->assertTrue($result, '一時フォルダが正しく生成されていません');

	}

	/**
	 * フォルダの中をフォルダを残して空にする
	 */
	public function testEmptyFolder()
	{

		$dummyPath = TMP . 'test' . DS;
		$names = [
			'folder' => ['folder1', 'folder2'],
			'file' => ['file1', 'file2'],
		];

		// ダミーのフォルダとファイルを作成
		$Folder = new Folder();
		$Folder->create($dummyPath, 0755);
		$Folder->create($dummyPath . $names['folder'][0], 0755);
		$Folder->create($dummyPath . $names['folder'][1], 0755);

		$File1 = new File($dummyPath . $names['file'][0], true);
		$File2 = new File($dummyPath . $names['file'][1], true);

		emptyFolder($dummyPath);

		$result = true;
		// フォルダが存在しているかチェック
		foreach($names['folder'] as $key => $name) {
			if (!is_dir($dummyPath . $name)) {
				$result = false;
			}
			@rmdir($dummyPath . $name);
		}
		// ファイルが削除されているかチェック
		foreach($names['file'] as $key => $name) {
			if (file_exists($dummyPath . $name)) {
				$result = false;
			}
			@unlink($dummyPath . $name);
		}
		$Folder->delete($dummyPath);

		$this->assertTrue($result, 'フォルダの中のファイルのみを削除することができません');
	}


	/**
	 * 現在のビューディレクトリのパスを取得する
	 */
	public function testGetViewPath()
	{
		// テーマが設定されている場合
		Configure::write('BcSite.theme', 'hoge');
		$result = getViewPath();
		$expect = WWW_ROOT . 'theme' . DS . 'hoge' . DS;
		$this->assertEquals($expect, $result, '取得した現在のビューディレクトリのパスが正しくありません');

		// テーマが未設定の場合
		Configure::write('BcSite.theme', null);
		$result = getViewPath();
		$expect = APP . 'View' . DS;
		$this->assertEquals($expect, $result, '取得した現在のビューディレクトリのパスが正しくありません');
	}

	/**
	 * ファイルポインタから行を取得し、CSVフィールドを処理する
	 *
	 * @param string $content CSVの内容
	 * @param int $length length
	 * @param string $d delimiter
	 * @param string $e enclosure
	 * @param string $expext 期待値
	 * @param string $message テスト失敗時に表示するメッセージ
	 * @dataProvider fgetcsvRegDataProvider
	 */
	public function testFgetcsvReg($content, $length, $d, $e, $expect, $message)
	{
		$csv = new File(CACHE . 'test.csv');
		$csv->write($content);
		$csv->close();
		$csv->open();

		$result = fgetcsvReg($csv->handle, $length, $d, $e);
		$this->assertEquals($expect, $result, $message);

		$csv->close();
	}

	public function fgetcsvRegDataProvider()
	{
		return [
			['test1,test2,test3', null, ',', '"', ['test1', 'test2', 'test3'], 'ファイルポインタから行を取得し、CSVフィールドを正しく処理できません'],
			['test1,test2,test3', 5, ',', '"', ['test'], '読み込む文字列の長さを指定できません'],
			['test1?test2?test3', null, '?', '"', ['test1', 'test2', 'test3\\'], 'デリミタを指定できません'],
			['test1,<<test2,test3<<', null, ',', '<<', ['test1', 'test2,test3'], 'enclosureを指定できません'],
		];
	}

	/**
	 * httpからのフルURLを取得する
	 */
	public function testFullUrl()
	{
		$this->assertRegExp('/\//', fullUrl('/'));
		$this->assertRegExp('/\/.*blog/', fullUrl('/blog'));
		$this->assertRegExp('/\//', fullUrl(null));
	}

	/**
	 * サイトのトップレベルのURLを取得する
	 */
	public function testTopLevelUrl()
	{
		if (isConsole()) {
			$this->assertEquals('http://localhost', topLevelUrl());
		} else {
			$this->assertRegExp('/^http:\/\/.*\/$/', topLevelUrl());
			$this->assertRegExp('/^http:\/\/.*[^\/]$/', topLevelUrl(false));

			// httpsの場合
			$_SERVER['HTTPS'] = 'on';
			$this->assertRegExp('/^https:\/\//', topLevelUrl());
		}
	}

	/**
	 * サイトの設置URLを取得する
	 */
	public function testSiteUrl()
	{
		if (isConsole()) {
			$this->assertEquals('http://localhost/', siteUrl());
		} else {
			$topLevelUrl = topLevelUrl(false);

			Configure::write('App.baseUrl', '/test/');
			$this->assertEquals($topLevelUrl . '/test/', siteUrl());

			Configure::write('App.baseUrl', '/test/index.php');
			$this->assertEquals($topLevelUrl . '/test/', siteUrl());

			Configure::write('App.baseUrl', '/test/hoge/');
			$this->assertEquals($topLevelUrl . '/test/hoge/', siteUrl());
		}
	}

	/**
	 * 配列を再帰的に上書きする
	 */
	public function testAmr()
	{
		$a = ['a1', 'a2', 'a3'];
		$b = ['b1', 'b2'];

		// 1次元配列
		$this->assertEquals(['b1', 'b2', 'a3'], amr($a, $b));

		// 2次元配列
		$b = [['b1']];
		$this->assertEquals([['b1'], 'a2', 'a3'], amr($a, $b));

		// 3次元配列
		$a = [['a1'], 'a2', 'a3'];
		$b = [[['b1']]];
		$this->assertEquals([[['b1']], 'a2', 'a3'], amr($a, $b));
	}

	/**
	 * URLにセッションIDを付加する
	 */
	public function testAddSessionId()
	{
		// 初期化
		$sessionId = session_id();
		$sessionName = session_name();
		$_SERVER['REQUEST_URI'] = '/m/';
		$message = 'URLにセッションIDを正しく付加できません';
		$this->assertEquals('/?' . $sessionName . '=' . $sessionId, addSessionId('/', true), $message);
		$this->assertEquals('/?id=1&' . $sessionName . '=' . $sessionId, addSessionId('/?id=1', true), $message);
		$this->assertEquals('/?id=1&' . $sessionName . '=' . $sessionId, addSessionId('/?id=1&BASERCMS=1', true), $message);

		// urlが配列の場合
		$url = [
			0 => '/',
			'?' => [
				'id' => 1,
				'BASERCMS' => 1
			]
		];
		$expect = [
			0 => '/',
			'?' => [
				'id' => 1,
				$sessionName => $sessionId
			]
		];
		$this->assertEquals($expect, addSessionId($url, true), $message);
	}

	/**
	 * 利用可能なプラグインのリストを取得する
	 */
	public function testGetEnablePlugins()
	{
		$result = getEnablePlugins();
		$pluginNames = [
			$result[0]['Plugin']['name'],
			$result[1]['Plugin']['name'],
			$result[2]['Plugin']['name']
		];
		$expect = ['Blog', 'Feed', 'Mail'];
		$this->assertEquals($expect, $pluginNames, '利用可能なプラグインのリストを正しく取得できません');
	}

	/**
	 * サイト基本設定をConfigureへ読み込む
	 */
	public function testLoadSiteConfig()
	{
		// 通常読み込み
		Configure::write('BcSite', null);
		loadSiteConfig();
		$this->assertArrayHasKey('name', Configure::read('BcSite'));
		// 強制読み込み
		/* @var SiteConfig $SiteConfig */
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$siteConfigs = $SiteConfig->findExpanded();
		$siteConfigs['SiteConfig']['name'] = 'hoge';
		$SiteConfig->saveKeyValue($siteConfigs);
		$this->assertNotEquals('hoge', Configure::read('BcSite.name'));
		loadSiteConfig(true);
		$this->assertEquals('hoge', Configure::read('BcSite.name'));
	}

	/**
	 * バージョンを取得する
	 */
	public function testGetVersion()
	{
		// BaserCMSコアのバージョン取得
		$result = getVersion();
		$version = file(BASER . 'VERSION.txt');
		$Bcversion = substr($version[0], 0, -1);
		$this->assertEquals($Bcversion, $result, 'BaserCMSコアのバージョンを正しく取得できません');

		$result = getVersion('Blog');
		$this->assertEquals($Bcversion, $result, 'BaserCMSコアのバージョンを正しく取得できません');

		// プラグインのバージョンを取得
		// ダミーのプラグインを作成
		$path = APP . 'Plugin' . DS . 'Hoge' . DS;
		$Folder = new Folder($path, true);
		$File = new File($path . 'VERSION.txt', true);
		$File->write('1.2.3');
		$result = getVersion('Hoge');

		$File->close();
		$Folder->delete();
		$this->assertEquals('1.2.3', $result, 'プラグインのバージョンを取得できません');
	}

	/**
	 * アップデートのURLを記載したメールを送信する
	 */
	public function testSendUpdateMail()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 展開出力
	 */
	public function testP()
	{
		ob_start();
		p(['test']);
		$result = ob_get_clean();
		$expect = 'array.*int.*0.*=&gt; &#039;test&#039;';
		$this->assertRegExp('/' . $expect . '/s', $result);
	}

	/**
	 * データベースのドライバー名を取得する
	 */
	public function testGetDbDriver()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンソールから実行されているかチェックする
	 */
	public function testIsConsole()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Constructs associative array from pairs of arguments.
	 */
	public function testAa()
	{
		$result = aa('a', 'b', 'c');
		$expect = ['a' => 'b', 'c' => null];
		$this->assertEquals($expect, $result);
	}

	/**
	 * 日本語ファイル名対応版basename
	 */
	public function testMb_basename()
	{
		$result = mb_basename('/hoge/あいうえお.php');
		$this->assertEquals('あいうえお.php', $result);

		$result = mb_basename('/hoge/あいうえお.phptest', 'test');
		$this->assertEquals('あいうえお.php', $result, 'suffixを取り除けません');

		$result = mb_basename('/hoge/あいうえおtest.php', 'test');
		$this->assertEquals('あいうえおtest.php', $result);
	}

	/**
	 * プラグインを読み込む
	 * Blogプラグインでテストする前提
	 * TODO 一部未完成 引数$priorityが機能していないバグ？があります
	 *
	 *
	 * @param string $plugin プラグイン名
	 * @dataProvider loadPluginDataProvider
	 */
	public function testLoadPlugin($plugin, $priority, $expect)
	{
		// 他のテストに影響がでるためバックアップをとる
		$buckupPlugins = CakePlugin::loaded();
		$buckupBlog = Configure::read('BcApp.adminNavi.blog');

		Configure::delete('BcApp.adminNavi.blog');
		CakePlugin::unload();

		// プラグインを読み込む
		$result = loadPlugin($plugin, $priority);
		$this->assertEquals($expect, $result);

		/**
		 * BlogControllerEventListenerの第１メソッドを利用してテストを実行
		 */
		if ($expect) {
			// プラグインが読み込めているか
			$this->assertContains($plugin, CakePlugin::loaded(), 'プラグインを読み込めません');
			$this->assertNotNull(Configure::read('BcApp.adminNavi.blog'), 'プラグインの設定が正しく設定されていません');

			$event = new CakeEventManager();
			$EventListeners = $event->listeners('Controller.Contents.beforeDelete');

			// イベントリスナーに登録されているか
			$this->assertContains('contentsBeforeDelete', $EventListeners[0]['callable'], 'プラグインイベントを正しく登録できません');

			// プライオリティを設定できているか
			if (!is_null($priority)) {
				$this->assertEquals($priority, $EventListeners[1]['callable'][0]->events['Contents.beforeDelete']['priority']);
			}
		}

		// バックアップを復元
		Configure::write('BcApp.adminNavi.blog', $buckupBlog);
		foreach($buckupPlugins as $key => $value) {
			CakePlugin::load($value);
		}

	}

	public function loadPluginDataProvider()
	{
		return [
			['Blog', null, true],
			['Blog', 1, true],
			['Hoge', null, false],
		];
	}

	/**
	 * 後方互換の為の非推奨メッセージを生成する
	 */
	public function testDeprecatedMessage()
	{
		$result = deprecatedMessage('target', 'since', 'remove', 'note');
		$expect = 'target は、バージョン since より非推奨となりました。バージョン remove で削除される予定です。note';
		$this->assertEquals($expect, $result);

		// デバックモードではない場合
		Configure::write('debug', 0);
		$result = deprecatedMessage('target', 'since', 'remove', 'note');
		$this->assertNull($result);
	}

	/**
	 * パーセントエンコーディングされないURLセーフなbase64エンコード
	 */
	public function testBase64UrlsafeEncodeDecode()
	{
		// encode
		$text = 'ふぁsdlfdfがgふぁsdlpfs'; // base64エンコードすると + と = が含まれる文字列
		$enc = base64UrlsafeEncode($text);
		$result = urlencode($enc);
		// %が含まれてないかチェック
		$this->assertRegExp('/^(?!.*%)/', $result, 'パーセントエンコーディングされています');

		// decode
		$dec = base64UrlsafeDecode($enc);
		$this->assertEquals($dec, $text, '正しくデコードできません');
	}

	/**
	 * 実行環境のOSがWindowsであるかどうかを返す
	 */
	public function testIsWindows()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * 時刻の有効性チェックを行う
	 */
	public function testChecktime()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 関連するテーブルリストを取得する
	 */
	public function testGetTableList()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
