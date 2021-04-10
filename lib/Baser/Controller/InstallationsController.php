<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class InstallationsController
 *
 * インストーラーコントローラー
 *
 * @package Baser.Controller
 * @property BcManagerComponent $BcManager
 */
class InstallationsController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 * @access    public
	 */
	public $name = 'Installations';

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['Session', 'BcEmail', 'BcManager'];

	/**
	 * レイアウトパス
	 *
	 * @var string
	 * @access    public
	 */
	public $layoutPath = 'admin';

	/**
	 * サブフォルダ
	 *
	 * @var string
	 * @access    public
	 */
	public $subDir = 'admin';

	/**
	 * ヘルパー
	 *
	 * @var array
	 * @access    public
	 */
	public $helpers = ['BcHtml', 'BcForm', 'Js', 'BcTime'];

	/**
	 * モデル
	 *
	 * @var array
	 * @access    public
	 */
	public $uses = null;

	/**
	 * テーマ
	 *
	 * @var string
	 */
	public $theme = 'Baseradmin';

	public $isInstalled = false;

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		set_time_limit(300);
		/* インストール状態判別 */
		if (file_exists(APP . 'Config' . DS . 'database.php')) {
			ConnectionManager::sourceList();
			$db = ConnectionManager::$config;
			if ($db->default['datasource'] != '') {
				$installed = 'complete';
			} else {
				$installed = 'half';
			}
		} else {
			$installed = 'yet';
		}

		switch($this->request->action) {
			case 'alert':
				break;
			case 'reset':
				if (Configure::read('debug') != -1) {
					$this->notFound();
				}
				break;
			default:
				if ($installed === 'complete') {
					if ($this->request->action !== 'step5') {
						$this->notFound();
					}
				} else {
					$installationData = Cache::read('Installation', 'default');
					if (empty($installationData['lastStep'])) {
						if (Configure::read('debug') == 0) {
							$this->redirect(['action' => 'alert']);
							return;
						}
					}
				}
				break;
		}

		/*if (strpos($this->request->webroot, 'webroot') === false) {
			$this->request->webroot = DS;
		}*/

		$this->Security->csrfCheck = false;
		$this->Security->validatePost = false;
	}

	/**
	 * Step 1: ウェルカムページ
	 *
	 * @return void
	 */
	public function index()
	{
		$this->pageTitle = __d('baser', 'baserCMSのインストール');
		clearAllCache();
	}

	/**
	 * Step 2: 必須条件チェック
	 *
	 * @return void
	 */
	public function step2()
	{
		if ($this->request->data && $this->request->data['clicked'] === 'next') {
			$this->redirect('step3');
			return;
		}

		$checkResult = $this->BcManager->checkEnv();

		/* ダミーのデータベース設定ファイルを保存 */
		$this->BcManager->createDatabaseConfig();

		$this->set($checkResult);

		extract($checkResult);

		$this->set('blRequirementsMet', ($phpXml && $phpGd && $tmpDirWritable && $pagesDirWritable && $configDirWritable && $phpVersionOk && $themeDirWritable && $imgDirWritable && $jsDirWritable && $cssDirWritable));
		$this->pageTitle = __d('baser', 'baserCMSのインストール｜ステップ２');
	}

	/**
	 * Step 3: データベースの接続設定
	 *
	 * @return void
	 */
	public function step3()
	{
		$dbsource = $this->_getDbSource();

		if (!$this->request->data) {
			clearAllCache();
			$this->request->data = $this->_getDefaultValuesStep3();
			$this->set('dbDataPatterns', $this->BcManager->getAllDefaultDataPatterns());
			$this->pageTitle = __d('baser', 'baserCMSのインストール｜ステップ３');
			$this->set('dbsource', $dbsource);
			return;
		}

		$this->_writeDbSettingToSession($this->request->data['Installation']);

		/* 戻るボタンクリック時 */
		if ($this->request->data['buttonclicked'] === 'back') {
			$this->redirect('step2');
			return;

			/* 接続テスト */
		}

		if ($this->request->data['buttonclicked'] === 'checkdb') {

			$this->set('blDBSettingsOK', $this->_testConnectDb($this->_readDbSetting()));

			/* 「次のステップへ」クリック時 */
		} elseif ($this->request->data['buttonclicked'] === 'createdb') {

			ini_set("max_execution_time", 180);

			$dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
			if (isset($this->request->data['Installation']['dbDataPattern'])) {
				$dbDataPattern = $this->request->data['Installation']['dbDataPattern'];
			}
			$result = false;
			$errorMessage = __d('baser', 'データベースの構築中にエラーが発生しました。');
			try {
				$this->_deleteAllTables();
				$result = $this->_constructionDb($dbDataPattern, Configure::read('BcApp.defaultAdminTheme'));
			} catch (Exception $e) {
				$errorMessage .= "\n" . $e->getMessage();
			}
			if ($result) {
				$this->BcMessage->setInfo(__d('baser', 'データベースの構築に成功しました。'));
				$this->redirect('step4');
				return;
			}

			$this->BcMessage->setError($errorMessage);
		}

		$this->set('dbDataPatterns', $this->BcManager->getAllDefaultDataPatterns());
		$this->pageTitle = __d('baser', 'baserCMSのインストール｜ステップ３');
		$this->set('dbsource', $dbsource);
	}

	/**
	 * Step 4: データベース生成／管理者ユーザー作成
	 *
	 * @return void
	 */
	public function step4()
	{
		if (!$this->request->data) {
			$this->request->data = $this->_getDefaultValuesStep4();
			$this->pageTitle = __d('baser', 'baserCMSのインストール｜ステップ４');
			return;
		}

// ユーザー情報をセッションに保存
		$this->Session->write('Installation.admin_email', $this->request->data['Installation']['admin_email']);
		$this->Session->write('Installation.admin_username', $this->request->data['Installation']['admin_username']);
		$this->Session->write('Installation.admin_password', $this->request->data['Installation']['admin_password']);

		if ($this->request->data['Installation']['clicked'] === 'back') {

			$this->redirect('step3');
			return;
		}

		if ($this->request->data['Installation']['clicked'] === 'finish') {

			// DB接続
			$db = $this->BcManager->connectDb($this->_readDbSetting());

			// サイト基本設定登録
			$this->BcManager->setAdminEmail($this->request->data['Installation']['admin_email']);

			// SecuritySalt設定
			$salt = $this->BcManager->setSecuritySalt();
			$this->Session->write('Installation.salt', $salt);
			// SecurityCipherSeed設定
			$cipherSeed = $this->BcManager->setSecurityCipherSeed();
			$this->Session->write('Installation.cipherSeed', $cipherSeed);

			// 管理ユーザー登録
			$user = [
				'name' => $this->request->data['Installation']['admin_username'],
				'password_1' => $this->request->data['Installation']['admin_password'],
				'password_2' => $this->request->data['Installation']['admin_confirmpassword'],
				'email' => $this->request->data['Installation']['admin_email']
			];

			if ($this->BcManager->addDefaultUser($user)) {
				$this->_sendCompleteMail($user['email'], $user['name'], $user['password_1']);
				$this->redirect('step5');
				return;
			}

			$User = ClassRegistry::init('User', 'Model');
			if (!empty($User->validationErrors)) {
				$errMsg = implode("\n", Hash::extract($User->validationErrors, '{s}.{n}'));
			}
			$this->BcMessage->setError(__d('baser', '管理ユーザーを作成できませんでした。'));
			$this->BcMessage->setError($errMsg);
		}
		$this->pageTitle = __d('baser', 'baserCMSのインストール｜ステップ４');
	}

	/**
	 * インストール完了メールを送信する
	 *
	 * @param string $email
	 * @param string $name
	 * @param string $password
	 * @return void
	 */
	protected function _sendCompleteMail($email, $name, $password)
	{
		if (DS !== '\\') {
			$body = ['name' => $name, 'password' => $password, 'siteUrl' => siteUrl()];
			$this->sendMail($email, __d('baser', 'baserCMSインストール完了'), $body, ['template' => 'installed', 'from' => $email]);
		}
	}

	/**
	 * Step 5: 設定ファイルの生成
	 * データベース設定ファイル[database.php]
	 * インストールファイル[install.php]
	 *
	 * @return void
	 */
	public function step5()
	{
		$this->pageTitle = __d('baser', 'baserCMSのインストール完了！');
		Cache::config('default', ['engine' => 'File']);

		if (!BC_INSTALLED) {
			$installationData = $this->Session->read('Installation');
			$installationData['lastStep'] = true;
			checkTmpFolders();
			Configure::write('Cache.disable', false);
			// インストールファイルでセッションの保存方法を切り替える為、インストール情報をキャッシュに保存
			Cache::write('Installation', $installationData, 'default');
			// データベース設定を書き込む
			$this->BcManager->createDatabaseConfig($this->_readDbSetting());
			// インストールファイルを生成する
			$secritySalt = $this->Session->read('Installation.salt');
			$secrityCipherSeed = $this->Session->read('Installation.cipherSeed');
			$this->BcManager->createInstallFile($secritySalt, $secrityCipherSeed);

			//==================================================================
			// BcManagerComponent::createPageTemplates() を実行する際、
			// 固定ページでプラグインを利用している場合あり、プラグインがロードされていないとエラーになる為、
			// リダイレクト前にコアプラグインの有効化とテーマ保有のプラグインのインストールを完了させておく
			// =================================================================
			$dbConfig = $this->_readDbSetting(Cache::read('Installation', 'default'));

			// データベースのデータを初期設定に更新
			$this->BcManager->executeDefaultUpdates($dbConfig);

			// テーマを配置する
			$this->BcManager->deployTheme();

			$dbDataPattern = $this->Session->read('Installation.dbDataPattern');
			list($theme, $pattern) = explode('.', $dbDataPattern);
			loadSiteConfig();

			$this->BcManager->installCorePlugin($dbConfig, $dbDataPattern);

			App::build(['Plugin' => array_merge([BASER_THEMES . Configure::read('BcSite.theme') . DS . 'Plugin' . DS], App::path('Plugin'))]);
			$themesPlugins = BcUtil::getCurrentThemesPlugins();
			if ($themesPlugins) {
				foreach($themesPlugins as $plugin) {
					$this->BcManager->installPlugin($plugin);
					CakePlugin::load($plugin);
					$this->BcManager->resetTables('plugin', $dbConfig = null, $plugin);
					$this->BcManager->loadDefaultDataPattern('plugin', null, $pattern, $theme, $plugin);
				}
			}

			$Db = ConnectionManager::getDataSource('default');
			if ($Db->config['datasource'] === 'Database/BcPostgres') {
				$Db->updateSequence();
			}

			clearAllCache();
			if (function_exists('opcache_reset')) {
				opcache_reset();
			}
			$this->redirect('step5');
			return;
		}

		$installationData = Cache::read('Installation', 'default');
		if (empty($installationData['lastStep'])) {
			return;
		}

		// ログイン
		$this->_login();

		// テーマに管理画面のアセットへのシンボリックリンクを作成する
		$this->BcManager->deployAdminAssets();

		// アップロード用初期フォルダを作成する
		$this->BcManager->createDefaultFiles();

		// エディタテンプレート用の画像を配置
		$this->BcManager->deployEditorTemplateImage();

		// Pagesファイルを生成する
		$this->BcManager->createPageTemplates();

		$this->Session->delete('InstallLastStep');
	}

	/**
	 * 管理画面にログインする
	 *
	 * @return void
	 * @access    protected
	 */
	protected function _login()
	{
		$extra = [];
		// ログインするとセッションが初期化されてしまうので一旦取得しておく
		$installationSetting = Cache::read('Installation', 'default');
		Cache::delete('Installation', 'default');
		Configure::write('Security.salt', $installationSetting['salt']);
		$extra['data']['User']['name'] = $installationSetting['admin_username'];
		$extra['data']['User']['password'] = $installationSetting['admin_password'];
		$this->requestAction(['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login_exec'], $extra);
		$this->Session->write('Installation', $installationSetting);
	}

	/**
	 * データベースを構築する
	 *
	 * @param string $dbDataPattern データパターン
	 * @return boolean
	 */
	protected function _constructionDb($dbDataPattern = null, $adminTheme = '')
	{
		$dbConfig = $this->_readDbSetting();
		if (!$this->BcManager->constructionDb($dbConfig, $dbDataPattern, $adminTheme)) {
			return false;
		}
		return true;
	}

	/**
	 * ステップ３用のフォーム初期値を取得する
	 *
	 * @return array
	 * @access    protected
	 */
	protected function _getDefaultValuesStep3()
	{
		$defaultTheme = Configure::read('BcApp.defaultTheme');
		$data = [];
		if (!$this->Session->read('Installation.dbType')) {
			$data['Installation']['dbType'] = 'mysql';
			$data['Installation']['dbHost'] = 'localhost';
			$data['Installation']['dbPort'] = '3306';
			$data['Installation']['dbPrefix'] = 'mysite_';
			$data['Installation']['dbName'] = 'basercms';
			$data['Installation']['dbDataPattern'] = $defaultTheme . '.default';
			return $data;
		}

		$_data = $this->_readDbSetting();
		$data['Installation']['dbType'] = $_data['datasource'];
		$data['Installation']['dbHost'] = $_data['host'];
		$data['Installation']['dbPort'] = $_data['port'];
		$data['Installation']['dbPrefix'] = $_data['prefix'];
		$_data['database'] = basename($_data['database']);
		$_data['database'] = str_replace(['.csv', '.db'], '', $_data['database']);
		$_data['database'] = basename($_data['database']);
		$data['Installation']['dbName'] = $_data['database'];
		$data['Installation']['dbUsername'] = $_data['login'];
		$data['Installation']['dbPassword'] = $_data['password'];
		$data['Installation']['dbDataPattern'] = $_data['dataPattern'];

		return $data;
	}

	/**
	 * ステップ４用のフォーム初期値を取得する
	 *
	 * @return array
	 * @access    protected
	 */
	protected function _getDefaultValuesStep4()
	{
		$data = [];
		if ($this->Session->read('Installation.admin_username')) {
			$data['Installation']['admin_username'] = $this->Session->read('Installation.admin_username');
		} else {
			$data['Installation']['admin_username'] = '';
		}
		if ($this->Session->read('Installation.admin_password')) {
			$data['Installation']['admin_password'] = $this->Session->read('Installation.admin_password');
			$data['Installation']['admin_confirmpassword'] = $data['Installation']['admin_password'];
		} else {
			$data['Installation']['admin_password'] = '';
		}
		if ($this->Session->read('Installation.admin_email')) {
			$data['Installation']['admin_email'] = $this->Session->read('Installation.admin_email');
		} else {
			$data['Installation']['admin_email'] = '';
		}
		return $data;
	}

	/**
	 * DB設定をセッションから取得
	 *
	 * @return array
	 * @access    protected
	 */
	protected function _readDbSetting($installationData = [])
	{
		if (!$installationData) {
			if ($this->Session->check('Installation')) {
				$installationData = $this->Session->read('Installation');
			} else {
				$installationData = [
					'dbType' => '',
					'dbHost' => '',
					'dbPort' => '',
					'dbUsername' => '',
					'dbPassword' => '',
					'dbPrefix' => '',
					'dbName' => '',
					'dbSchema' => '',
					'dbEncoding' => '',
					'dbDataPattern' => ''
				];
			}
		}
		$data = [
			'datasource' => $installationData['dbType'],
			'host' => $installationData['dbHost'],
			'port' => $installationData['dbPort'],
			'login' => $installationData['dbUsername'],
			'password' => $installationData['dbPassword'],
			'prefix' => $installationData['dbPrefix'],
			'database' => $this->BcManager->getRealDbName($installationData['dbType'], $installationData['dbName']),
			'schema' => $installationData['dbSchema'],
			'encoding' => $installationData['dbEncoding'],
			'dataPattern' => $installationData['dbDataPattern'],
			'persistent' => false,
		];
		return $data;
	}

	/**
	 * DB設定をセッションに保存
	 *
	 * @param array $data
	 * @return void
	 * @access    protected
	 */
	protected function _writeDbSettingToSession($data)
	{
		/* dbEncoding */
		$data['dbEncoding'] = 'utf8';

		/* dbSchema */
		if ($data['dbType'] === 'postgres') {
			$data['dbSchema'] = 'public'; // TODO とりあえずpublic固定
		} else {
			$data['dbSchema'] = '';
		}
		if ($data['dbType'] === 'csv') {
			$data['dbEncoding'] = 'sjis';
		}

		$this->Session->write('Installation.dbType', $data['dbType']);
		$this->Session->write('Installation.dbHost', $data['dbHost']);
		$this->Session->write('Installation.dbPort', $data['dbPort']);
		$this->Session->write('Installation.dbUsername', $data['dbUsername']);
		$this->Session->write('Installation.dbPassword', $data['dbPassword']);
		$this->Session->write('Installation.dbPrefix', $data['dbPrefix']);
		$this->Session->write('Installation.dbName', $data['dbName']);
		$this->Session->write('Installation.dbSchema', $data['dbSchema']);
		$this->Session->write('Installation.dbEncoding', $data['dbEncoding']);
		$this->Session->write('Installation.dbDataPattern', $data['dbDataPattern']);
	}

	/**
	 * データベース接続テスト
	 *
	 * @param array $config
	 * @return boolean
	 */
	protected function _testConnectDb($config)
	{
		/* データベース接続確認 */
		try {
			$this->BcManager->checkDbConnection($config);
		} catch (Exception $e) {
			$message = __d('baser', 'データベースへの接続でエラーが発生しました。データベース設定を見直してください。');
			if (preg_match('/with message \'(.+?)\' in/s', $e->getMessage(), $matches)) {
				$message .= "\n" . $matches[1];
			}
			$this->BcMessage->setError(__d('baser', "データベースへの接続でエラーが発生しました。データベース設定を見直してください。\nサーバー上に指定されたデータベースが存在しない可能性が高いです。"));
			return false;
		}

		/* データベース接続生成 */
		$db = $this->BcManager->connectDb($config);

		if (!$db->connected) {

			if (!$this->Session->read('Message.flash.message')) {
				if (!$db->connection) {
					$this->BcMessage->setError(
						__d('baser', 'データベースへの接続でエラーが発生しました。データベース設定を見直してください。')
					);
					return false;
				}

				$this->BcMessage->setError(
					__d('baser', "データベースへの接続でエラーが発生しました。データベース設定を見直してください。\nサーバー上に指定されたデータベースが存在しない可能性が高いです。")
				);
			}
			return false;
		}

		//version check
		switch(get_class($db)) {
			case 'BcMysql' :
				$result = $db->query("SELECT version() as version");
				if (version_compare($result[0][0]['version'], Configure::read('BcRequire.MySQLVersion')) == -1) {
					$this->BcMessage->setError(sprintf(__d('baser', 'データベースのバージョンが %s 以上か確認してください。'), Configure::read('BcRequire.MySQLVersion')));
					return false;
				}
				break;
			case 'BcPostgres' :
				$result = $db->query("SELECT version() as version");
				list(, $version) = explode(" ", $result[0][0]['version']);
				if (version_compare(trim($version), Configure::read('BcRequire.PostgreSQLVersion')) == -1) {
					$this->BcMessage->setError(sprintf(__d('baser', 'データベースのバージョンが %s 以上か確認してください。'), Configure::read('BcRequire.PostgreSQLVersion')));
					return false;
				}
				break;
		}

		/* 一時的にテーブルを作成できるかテスト */
		$randomtablename = 'deleteme' . mt_rand(100, 100000);
		$result = $db->execute("CREATE TABLE $randomtablename (a varchar(10))");


		if (!$result) {
			$this->BcMessage->setError(__d('baser', "データベースへの接続でエラーが発生しました。\n") . $db->error);
			return false;
		}

		$db->execute('drop TABLE ' . $randomtablename);
		$this->BcMessage->setInfo(__d('baser', 'データベースへの接続に成功しました。'));

		// データベースのテーブルをチェック
		$tableNames = $db->listSources();
		$prefix = Hash::get($config, 'prefix');
		$duplicateTableNames = array_filter($tableNames, function($tableName) use ($prefix) {
			return strpos($tableName, $prefix) === 0;
		});

		if (count($duplicateTableNames) > 0) {
			$this->BcMessage->setInfo(
				__d(
					'baser',
					'データベースへの接続に成功しましたが、プレフィックスが重複するテーブルが存在します。') . implode(', ', $duplicateTableNames)
			);
		}

		return true;
	}

	/**
	 * 利用可能なデータソースを取得する
	 *
	 * @return array
	 * @access    protected
	 */
	protected function _getDbSource()
	{
		/* DBソース取得 */
		$dbsource = [];
		$folder = new Folder();
		$pdoDrivers = PDO::getAvailableDrivers();

		/* MySQL利用可否 */
		if (in_array('mysql', $pdoDrivers)) {
			$dbsource['mysql'] = 'MySQL';
		}

		/* PostgreSQL利用可否 */
		if (in_array('pgsql', $pdoDrivers)) {
			$dbsource['postgres'] = 'PostgreSQL';
		}

		/* SQLite利用可否チェック */
		if (in_array('sqlite', $pdoDrivers) && extension_loaded('sqlite3') && class_exists('SQLite3')) {
			$dbFolderPath = APP . 'db' . DS . 'sqlite';
			if (is_writable(dirname($dbFolderPath)) && $folder->create($dbFolderPath, 0777)) {
				$info = SQLite3::version();
				if (version_compare($info['versionString'], Configure::read('BcRequire.winSQLiteVersion'), '>')) {
					$dbsource['sqlite'] = 'SQLite';
				}
			}
		}

		/* CSV利用可否 */
		/* $dbFolderPath = APP.'db'.DS.'csv';
		  if(is_writable(dirname($dbFolderPath)) && $folder->create($dbFolderPath, 0777)){
		  $dbsource['csv'] = 'CSV';
		  } */

		return $dbsource;
	}

	/**
	 * インストール不能警告メッセージを表示
	 *
	 * @return void
	 */
	public function alert()
	{
		$this->pageTitle = __d('baser', 'baserCMSのインストールを開始できません');
	}

	/**
	 * baserCMSを初期化する
	 * debug フラグが -1 の場合のみ実行可能
	 *
	 * @return    void
	 * @access    public
	 */
	public function reset()
	{
		$this->pageTitle = __d('baser', 'baserCMSの初期化');
		$this->layoutPath = 'admin';
		$this->layout = 'default';
		$this->subDir = 'admin';

		if (empty($this->request->data['Installation']['reset'])) {
			$this->set('complete', !BC_INSTALLED? true : false);
			return;
		}

		$dbConfig = $this->_readDbSetting();
		if (!$dbConfig) {
			$dbConfig = getDbConfig('default');
		}

		if (!$this->BcManager->reset($dbConfig)) {
			$this->BcMessage->setError(
				__d(
					'baser',
					'baserCMSを初期化しましたが、正常に処理が行われませんでした。詳細については、エラー・ログを確認してださい。'
				)
			);
		} else {
			$this->BcMessage->setInfo(__d('baser', 'baserCMSを初期化しました。'));
		}
		$this->redirect('reset');
	}

	/**
	 * 全てのテーブルを削除する
	 *
	 * @return void
	 * @access public
	 */
	public function _deleteAllTables()
	{
		$dbConfig = $this->_readDbSetting();
		if (!$dbConfig) {
			$dbConfig = getDbConfig();
		}
		$this->BcManager->deleteTables('default', $dbConfig);
	}

}
