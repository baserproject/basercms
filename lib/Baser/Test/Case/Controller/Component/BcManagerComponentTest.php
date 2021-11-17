<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller.Component
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcManagerComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * Class BcManagerTestController
 *
 * @package Baser.Test.Case.Controller.Component
 * @property BcManagerComponent $BcManager
 */
class BcManagerTestController extends Controller
{

	public $components = ['BcManager'];

}

/**
 * Class BcManagerComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 * @property BcManagerComponent $BcManager
 */
class BcManagerComponentTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.BlogTag',
		'baser.Default.SearchIndex',
		'baser.Default.FeedDetail',
		'baser.Default.SiteConfig',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Page',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.User',
		'baser.Default.Site',
	];

	public $components = ['BcManager'];

	public function setUp()
	{
		parent::setUp();

		// コンポーネントと偽のテストコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcManagerTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->BcManager = new BcManagerComponent($collection);
		$this->BcManager->request = $request;
		$this->BcManager->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');
	}

	public function tearDown()
	{
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcManager);
	}

	public function test_getDataSource()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function test_updateContents()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function test_updatePluginStatus()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * baserCMSのインストール
	 *
	 * @param type $dbConfig
	 * @param type $adminUser
	 * @param type $adminPassword
	 * @param type $adminEmail
	 * @return boolean
	 */
	public function testInstall()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * データベースに接続する
	 */
	public function testConnectDb()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * datasource名を取得
	 *
	 * @param string $datasource datasource名
	 * @param string $expected 期待値
	 * @dataProvider getDatasourceNameDataProvider
	 */
	public function testGetDatasourceName($datasource, $expected)
	{
		$result = $this->BcManager->getDatasourceName($datasource);
		$this->assertEquals($expected, $result, 'datasource名を正しく取得できません');
	}

	public function getDatasourceNameDataProvider()
	{
		return [
			['postgres', 'Database/BcPostgres'],
			['mysql', 'Database/BcMysql'],
			['sqlite', 'Database/BcSqlite'],
			['csv', 'Database/BcCsv'],
		];
	}

	/**
	 * 実際の設定用のDB名を取得する
	 *
	 * @param string $type
	 * @param string $name
	 * @dataProvider getRealDbNameDataProvider
	 */
	public function testGetRealDbName($type, $name, $expected)
	{
		$result = $this->BcManager->getRealDbName($type, $name);
		$this->assertEquals($expected, $result, '実際の設定用のDB名を正しく取得できません');
	}

	public function getRealDbNameDataProvider()
	{
		return [
			['type', 'name', 'name'],
			['sqlite', 'name', APP . 'db' . DS . 'sqlite' . DS . 'name' . '.db'],
			['csv', 'name', APP . 'db' . DS . 'csv' . DS . 'name'],
			['sqlite', '/name', '/name'],
		];
	}

	/**
	 * テーマ用のページファイルを生成する
	 *
	 * @access    protected
	 */
	public function testCreatePageTemplates()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * データベースのデータに初期更新を行う
	 */
	public function testExecuteDefaultUpdates()
	{

		$dbConfig = [
			'datasource' => 'Database/BcMysql',
			'persistent' => false,
			'host' => 'localhost',
			'port' => '8889',
			'login' => 'root',
			'password' => 'root',
			'database' => 'basercms',
			'schema' => '',
			'prefix' => 'mysite_',
			'encoding' => 'utf8',
		];

		// プラグイン有効化チェック用準備(ダミーのプラグインディレクトリを作成)
		$testPluginPath = BASER_PLUGINS . 'Test' . DS;
		$testPluginConfigPath = $testPluginPath . 'config.php';
		$Folder = new Folder();
		$Folder->create($testPluginPath);
		$File = new File($testPluginConfigPath, true);
		$File->write('<?php $title = "テスト";');

		Configure::write('BcApp.corePlugins', ['Blog', 'Feed', 'Mail', 'Test']);


		// 初期更新を実行
		$result = $this->BcManager->executeDefaultUpdates($dbConfig);


		// =====================
		// プラグイン有効化チェック
		// =====================
		$File->delete();
		$Folder->delete($testPluginPath);

		$this->Plugin = ClassRegistry::init('Plugin');
		$plugin = $this->Plugin->find('first', [
				'conditions' => ['id' => 4],
				'fields' => ['title', 'status'],
			]
		);
		$expected = [
			'Plugin' => [
				'title' => 'テスト',
				'status' => 1,
			]
		];
		$this->Plugin->delete(4);
		unset($this->Plugin);
		$this->assertEquals($expected, $plugin, 'プラグインのステータスを正しく更新できません');
		$this->assertTrue($result, 'データベースのデータに初期更新に失敗しました');
	}

	/**
	 * サイト基本設定に管理用メールアドレスを登録する
	 */
	public function testSetAdminEmail()
	{

		$this->BcManager->setAdminEmail('hoge');

		$this->SiteConfig = ClassRegistry::init('SiteConfig');
		$result = $this->SiteConfig->find('first', [
				'conditions' => ['name' => 'email'],
				'fields' => ['value'],
			]
		);

		$this->assertEquals('hoge', $result['SiteConfig']['value'], 'サイト基本設定に管理用メールアドレスを登録できません');


	}

	/**
	 * 初期ユーザーを登録する
	 *
	 * @param array $user
	 * @return boolean
	 */
	public function testAddDefaultUser()
	{
		$user = [
			'name' => 'hoge',
			'email' => 'test@co.jp',
			'user_group_id' => 1,
			'password_1' => 'testtest',
			'password_2' => 'testtest'
		];
		$result = $this->BcManager->addDefaultUser($user, 'hogehoge');

		$this->User = ClassRegistry::init('User');
		$this->User->delete($result['User']['id']);
		$this->assertContains('hoge', $result['User'], 'ユーザーを登録できません');

		$result = Configure::read('Security.salt');
		$this->assertEquals('hogehoge', $result, 'SecuritySaltを設定できません');

	}

	/**
	 * データベース設定ファイル[database.php]を保存する
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function testCreateDatabaseConfig()
	{

		// database.phpをバックアップ
		$configPath = APP . 'Config' . DS;
		$copy = copy($configPath . 'database.php', $configPath . 'database.php.copy');

		if ($copy) {
			$options = [
				'datasource' => 'mysql',
				'host' => 'hoge',
				'port' => '0000',
			];
			$this->BcManager->createDatabaseConfig($options);

			$File = new File($configPath . 'database.php');
			$result = $File->read();

			// 生成されたファイルを削除し、バックアップしたファイルに置き換える
			$File->delete();
			$File->close();
			rename($configPath . 'database.php.copy', $configPath . 'database.php');

			$this->assertRegExp("/\\\$default.*'datasource' => 'Database\/BcMysql'.*'host' => 'hoge'.*'port' => '0000'/s", $result, 'データベース設定ファイル[database.php]を正しく保存できません');

		} else {
			$this->markTestIncomplete('database.phpのバックアップに失敗したため、このテストをスキップします。');
		}

	}

	/**
	 * インストール設定ファイルを生成する
	 */
	public function testCreateInstallFile()
	{

		// install.phpをバックアップ
		$configPath = APP . 'Config' . DS;
		$copy = copy($configPath . 'install.php', $configPath . 'install.php.copy');

		if ($copy) {

			$this->BcManager->createInstallFile('hogeSalt', 'hogeSeed', 'hogeUrl');

			$File = new File($configPath . 'install.php');
			$result = $File->read();

			// 生成されたファイルを削除し、バックアップしたファイルに置き換える
			$File->delete();
			$File->close();
			rename($configPath . 'install.php.copy', $configPath . 'install.php');

			$this->assertRegExp("/'Security.salt', 'hogeSalt'.*'Security.cipherSeed', 'hogeSeed'.*'BcEnv.siteUrl', 'hogeUrl'/s", $result, 'インストール設定ファイルを正しく生成できません');

		} else {
			$this->markTestIncomplete('install.phpのバックアップに失敗したため、このテストをスキップします。');

		}

	}

	/**
	 * セキュリティ用のキーを生成する
	 */
	public function testSetSecuritySalt()
	{

		$result = $this->BcManager->setSecuritySalt();
		$length = strlen($result);
		$this->assertEquals(40, $length, 'セキュリティ用のキーが指定した長さで生成されません');

		$result = $this->BcManager->setSecuritySalt(20);
		$length = strlen($result);
		$this->assertEquals(20, $length, 'セキュリティ用のキーが指定した長さで生成されません');

	}

	/**
	 * セキュリティ用の数字キーを生成する
	 */
	public function testSetSecurityCipherSeed()
	{

		$result = $this->BcManager->setSecurityCipherSeed();
		$length = strlen($result);
		$this->assertEquals(29, $length, 'セキュリティ用のキーが指定した長さで生成されません');

		$result = $this->BcManager->setSecurityCipherSeed(20);
		$length = strlen($result);
		$this->assertEquals(20, $length, 'セキュリティ用のキーが指定した長さで生成されません');

	}

	/**
	 * データベースを構築する
	 *
	 * @param array $dbConfig
	 * @param string $dbDataPattern
	 * @return boolean
	 */
	public function testConstructionDb()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * メール受信テーブルの再構築
	 *
	 * @return boolean
	 */
	public function testReconstructionMessage()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * 全ての初期データセットのリストを取得する
	 */
	public function testGetAllDefaultDataPatterns()
	{

		$result = $this->BcManager->getAllDefaultDataPatterns();
		$expecteds = [
			'core.default' => 'コア ( default )',
			'bccolumn.default' => 'bcColumn ( default )',
			'nada-icons.default' => 'nada icons ( default )',
		];
		foreach($expecteds as $expected) {
			$this->assertContains($expected, $result, '全ての初期データセットのリストを正しく取得できません');
		}

	}

	/**
	 * 初期データのセットを取得する
	 *
	 * @param string $theme
	 * @param array $options
	 * @return array
	 */
	public function testGetDefaultDataPatterns()
	{

		$options = ['useTitle' => false];
		$result = $this->BcManager->getDefaultDataPatterns('core', $options);
		$expected = [
			'core.default' => 'default'
		];
		$this->assertEquals($expected, $result, '初期データのセットのタイトルを外して取得できません');

	}

	/**
	 * 初期データを読み込む
	 *
	 * @param string $dbConfigKeyName
	 * @param array $dbConfig
	 * @param string $pattern
	 * @param string $theme
	 * @param string $plugin
	 * @return boolean
	 */
	public function testLoadDefaultDataPattern()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}


	public function testInitPlugin()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * システムデータを初期化する
	 *
	 * @param string $dbConfigKeyName
	 * @param array $dbConfig
	 */
	public function testInitSystemData()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * テーブルを構築する
	 *
	 * @param string $path
	 * @param string $dbConfigKeyName
	 * @param string $dbConfig
	 * @param string $dbDataPattern
	 * @return boolean
	 */
	public function testConstructionTable()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');


	}

	/**
	 * 全てのテーブルを削除する
	 *
	 * @param array $dbConfig
	 * @return boolean
	 */
	public function testDeleteAllTables($dbConfig = null)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * プラグインも含めて全てのテーブルをリセットする
	 *
	 * プラグインは有効となっているもののみ
	 * 現在のテーマでないテーマの梱包プラグインを検出できない為
	 *
	 * @param array $dbConfig
	 * @return boolean
	 */
	public function testResetAllTables($dbConfig = null, $excludes = [])
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * テーブルをリセットする
	 *
	 * @param type $dbConfigKeyName
	 * @param type $dbConfig
	 * @return boolean
	 */
	public function testResetTables()
	{

		$result = $this->BcManager->resetTables('test');
		$this->assertTrue($result, 'テーブルをリセットできません');

		$this->User = ClassRegistry::init('User');
		$User = $this->User->find('all', [
				'recursive' => -1,
			]
		);
		$this->assertEmpty($User, 'テーブルをリセットできません');

		$this->FeedDetail = ClassRegistry::init('FeedDetail');
		$FeedDetail = $this->FeedDetail->find('all', [
				'recursive' => -1,
			]
		);
		$this->assertEmpty($FeedDetail, 'プラグインのテーブルをリセットできません');

	}

	/**
	 * テーブルを削除する
	 */
	public function testDeleteTables()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * テーマを配置する
	 *
	 * @param string $theme テーマ名
	 * @dataProvider deployThemeDataProvider
	 */
	public function testDeployTheme($theme)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// nada-icons テーマフォルダを削除
		$Folder = new Folder();
		$targetPath = WWW_ROOT . 'theme' . DS . 'nada-icons';
		$Folder->delete($targetPath);

		$this->BcManager->deployTheme($theme);

		if ($theme != 'bccolumn') {
			$this->assertFileExists($targetPath, 'テーマを配置できません');

		} else {
			$this->assertFileNotExists($targetPath, '指定したテーマのみを配置することができません');
			$this->BcManager->deployTheme();

		}

	}

	public function deployThemeDataProvider()
	{
		return [
			[null],
			['nada-icons'],
			['bccolumn'],
		];
	}

	/**
	 * エディタテンプレート用のアイコン画像をデプロイ
	 *
	 * @return boolean
	 */
	public function testDeployEditorTemplateImage()
	{

		// editor フォルダを削除
		$Folder = new Folder();
		$targetPath = WWW_ROOT . 'files' . DS . 'editor' . DS;
		$Folder->delete($targetPath);

		$this->BcManager->deployEditorTemplateImage();

		$this->assertFileExists($targetPath, 'エディタテンプレート用のアイコン画像をデプロイできません');

	}

	/**
	 * アップロード用初期フォルダを作成する
	 */
	public function testCreateDefaultFiles()
	{

		// 各フォルダを削除
		$Folder = new Folder();
		$path = WWW_ROOT . 'files' . DS;
		$dirs = ['blog', 'editor', 'theme_configs'];

		foreach($dirs as $dir) {
			$Folder->delete($path . $dir);
		}

		$this->BcManager->createDefaultFiles();

		foreach($dirs as $dir) {
			$this->assertFileExists($path . $dir, 'アップロード用初期フォルダを正しく作成できません');
		}

	}

	/**
	 * 設定ファイルをリセットする
	 *
	 * @return boolean
	 */
	public function testResetSetting()
	{

		// database.phpとinstall.phpをバックアップ
		$configPath = APP . 'Config' . DS;
		$copy1 = copy($configPath . 'database.php', $configPath . 'database.php.copy');
		$copy2 = copy($configPath . 'install.php', $configPath . 'install.php.copy');

		if ($copy1 && $copy2) {

			$this->BcManager->resetSetting();

			$this->assertFileNotExists($configPath . 'database.php', '設定ファイルをリセットできません');
			$this->assertFileNotExists($configPath . 'install.php', '設定ファイルをリセットできません');

			// ファイルを復元
			rename($configPath . 'database.php.copy', $configPath . 'database.php');
			rename($configPath . 'install.php.copy', $configPath . 'install.php');

		} else {
			$this->markTestIncomplete('database.phpとinstall.phpのバックアップに失敗したため、このテストをスキップします。');

		}

	}

	/**
	 * files フォルダを初期化する
	 *
	 * @return boolean
	 */
	public function testResetFiles()
	{

		$this->BcManager->resetFiles();

		$path = WWW_ROOT . 'files' . DS;
		$dirs = ['blog', 'editor', 'theme_configs'];
		foreach($dirs as $dir) {
			$this->assertFileNotExists($path . $dir, 'files フォルダを初期化できません');
		}

		// 削除されたフォルダを復元
		$this->BcManager->createDefaultFiles();

	}

	/**
	 * 管理画面用のアセットフォルダ（img / js / css）を初期化する
	 *
	 * @return boolean
	 */
	public function testResetAdminAssets()
	{

		// 初期化
		$this->BcManager->resetAdminAssets();

		$paths = [
			WWW_ROOT . 'img' . DS . 'admin',
			WWW_ROOT . 'css' . DS . 'admin',
			WWW_ROOT . 'js' . DS . 'admin'
		];
		foreach($paths as $path) {
			$this->assertFileNotExists($path, '管理画面用のアセットフォルダを初期化できません');
		}

		// 復元
		$this->BcManager->deployAdminAssets();

	}

	/**
	 * baserCMSをリセットする
	 *
	 * @param array $dbConfig
	 */
	public function testReset()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	public function testResetPage()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	public function testResetThema()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	public function testEmptyFolder()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

	/**
	 * インストール設定を書き換える
	 *
	 * @param string $key
	 * @param string $value
	 * @return    boolean
	 * @access    public
	 */
	public function testSetInstallSetting()
	{

		// install.phpをバックアップ
		$configPath = APP . 'Config' . DS;
		$copy = copy($configPath . 'install.php', $configPath . 'install.php.copy');

		if ($copy) {

			$this->BcManager->setInstallSetting('hogeKey', 'hogeValue');

			$File = new File($configPath . 'install.php');
			$result = $File->read();

			// 生成されたファイルを削除し、バックアップしたファイルに置き換える
			$File->delete();
			$File->close();
			rename($configPath . 'install.php.copy', $configPath . 'install.php');

			$this->assertRegExp("/Configure::write\('hogeKey', hogeValue\)/s", $result, 'インストール設定ファイルを正しく書き換えることができません');

		} else {
			$this->markTestIncomplete('install.phpのバックアップに失敗したため、このテストをスキップします。');

		}

	}

	/**
	 * 環境チェック
	 *
	 * @return array
	 */
	public function testCheckEnv()
	{
		$result = $this->BcManager->checkEnv();
		$this->assertNotEmpty($result, '環境情報を取得できません');
	}

	/**
	 * DB接続チェック
	 *
	 * @param string $datasource 'MySQL' or 'Postgres' or 'SQLite' or 'CSV'
	 * @param string $database データベース名 SQLiteの場合はファイルパス CSVの場合はディレクトリへのパス
	 * @param string $host テキストDB or localhostの場合は不要
	 * @param string $port 接続ポート テキストDBの場合は不要
	 * @param string $login 接続ユーザ名 テキストDBの場合は不要
	 * @param string $password 接続パスワード テキストDBの場合は不要
	 */
	public function testCheckDbConnection()
	{

		// 使用しているDBのデータを取得し設定
		$dbData = ConnectionManager::getDataSource('default');

		$config = [
			'database' => $dbData->config['database'],
			'host' => $dbData->config['host'],
			'port' => $dbData->config['port'],
			'login' => $dbData->config['login'],
			'password' => $dbData->config['password'],
		];


		$datasource = $dbData->config['datasource'];
		switch($datasource) {
			case 'Database/BcPostgres' :
				$datasource = 'postgres';
				break;
			case 'Database/BcMysql' :
				$datasource = 'mysql';
				break;
			case 'Database/BcSqlite' :
				$datasource = 'sqlite';
				break;
			default :
		}

		$config['datasource'] = $datasource;


		$result = $this->BcManager->checkDbConnection($config);
		$this->assertTrue($result, 'DBに接続できません');


	}

	/**
	 * DB接続チェック
	 * checkDbConnection()の Exception 例外のテスト
	 *
	 * PHPUnitのバージョンによって、Exceptionは派生クラスではないとエラーが出るのでスキップ
	 *
	 * expectedException Exception
	 * expectedExceptionMessage ドライバが見つかりません Driver is not defined.(MySQL|Postgres|SQLite|CSV)
	 */

	public function testCheckDbConnectionException()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// でたらめな入力
		$config = [
			'datasource' => 'hoge',
			'database' => 'hoge',
			'host' => 'hoge',
			'port' => 0000,
			'login' => 'hoge',
			'password' => 'hoge',
		];
		$result = $this->BcManager->checkDbConnection($config);

	}

	/**
	 * DB接続チェック
	 * checkDbConnection()の PDOException 例外のテスト
	 */
	public function testCheckDbConnectionPDOException()
	{

		$this->expectException('PDOException');
		$this->expectExceptionMessage("SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed:");

		// でたらめな入力
		$config = [
			'database' => 'hoge',
			'host' => 'hoge',
			'port' => 0000,
			'login' => 'hoge',
			'password' => 'hoge',
		];

		// まともな datasource
		$dbData = ConnectionManager::getDataSource('default');
		$datasource = $dbData->config['datasource'];
		switch($datasource) {
			case 'Database/BcPostgres' :
				$datasource = 'postgres';
				$this->markTestIncomplete('postgresは、このテストの対象外です');
				break;
			case 'Database/BcMysql' :
				$datasource = 'mysql';
				break;
			case 'Database/BcSqlite' :
				$this->markTestIncomplete('sqliteは、このテストの対象外です');
			default :
		}
		$config['datasource'] = $datasource;

		$this->BcManager->checkDbConnection($config);

	}

	/**
	 * テーマに管理システム用アセットを配置する
	 */
	public function testDeployAdminAssets()
	{
		// 初期化
		$this->BcManager->resetAdminAssets();
		// 配置
		$this->BcManager->deployAdminAssets();

		if (is_dir(BASER_THEMES . Configure::read('BcSite.admin_theme'))) {
			$assetRoot = BASER_THEMES . Configure::read('BcSite.admin_theme') . DS;
		} else {
			$assetRoot = WWW_ROOT;
		}
		$paths = [
			$assetRoot . 'img' . DS . 'admin',
			$assetRoot . 'css' . DS . 'admin',
			$assetRoot . 'js' . DS . 'admin'
		];
		foreach($paths as $path) {
			$this->assertFileExists($path, '管理画面用のアセットフォルダを配置きません');
		}
	}

	/**
	 * プラグインをインストール/アンインストールする
	 *
	 * @param string $name
	 */
	public function testInstallAndUninstallPlugin()
	{

		mkdir(BASER_PLUGINS . 'Test');

		// -- インストール --
		$result = $this->BcManager->installPlugin('Test');
		rmdir(BASER_PLUGINS . 'Test');

		$this->assertTrue($result, 'プラグインをインストールできません');

		// インストールできたかDBチェック
		$this->Plugin = ClassRegistry::init('Plugin');
		$this->Plugin->cacheQueries = false;
		$data = $this->Plugin->find('first', [
				'conditions' => ['id' => 4],
			]
		);
		$this->assertEquals('Test', $data['Plugin']['name'], 'プラグインをインストールできません');

		// -- アンインストール --
		$result = $this->BcManager->uninstallPlugin('Test');
		$this->assertTrue($result, 'プラグインをアンインストールできません');

		$data = $this->Plugin->find('first', [
				'conditions' => ['id' => 4],
			]
		);
		$this->assertEquals(0, $data['Plugin']['status'], 'プラグインをアンインストールできません');

		unset($this->Plugin);
	}

	/**
	 * プラグインをインストールする
	 * 設定ファイルなどを読み込む場合
	 *
	 * @param string $name
	 */
	public function testInstallPluginInclude()
	{

		$pluginPath = BASER_PLUGINS . 'Test';
		mkdir($pluginPath);

		// -- init.php --
		mkdir($pluginPath . DS . 'Config');
		$Init = new File($pluginPath . DS . 'Config' . DS . 'init.php');
		$Init->write('');
		$Init->close();

		// インストール実行
		$result = $this->BcManager->installPlugin('Test');

		// 掃除
		$Init->delete();
		rmdir($pluginPath . DS . 'Config');

		$this->assertTrue($result, 'init.phpを読み込めません');

		// -- config.php --
		$Config = new File($pluginPath . DS . 'config.php');
		$Config->write('');
		$Config->close();

		// インストール実行
		$result = $this->BcManager->installPlugin('Test');

		// 掃除
		$Config->delete();
		rmdir($pluginPath);

		$this->assertTrue($result, 'config.phpを読み込めません');

	}

	/**
	 * テーマに梱包されているプラグインをインストールする
	 */
	public function testInstallThemesPlugins()
	{
		$theme = Configure::read('BcSite.theme');
		$Folder = new Folder();
		$path = BASER_THEMES . $theme . DS . 'Plugin';
		$Folder->delete($path);

		$result = $this->BcManager->installThemesPlugins($theme);
		$this->assertTrue($result, 'デフォルトテーマのプラグインのインストールに失敗しました。');

		$Folder->create($path . DS . 'dummy1');
		$result = $this->BcManager->installThemesPlugins($theme);
		$this->assertTrue($result, 'デフォルトテーマのプラグインのインストールに失敗しました。');
	}

	public function testStartup()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testUninstallPlugin()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
