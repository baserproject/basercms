<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller.Component
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Page', 'Model');
App::uses('Plugin', 'Model');
App::uses('User', 'Model');
App::uses('File', 'Utility');
App::uses('Component', 'Controller');
App::uses('ConnectionManager', 'Model');

/**
 * Class BcManagerComponent
 *
 * baser Manager コンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcManagerComponent extends Component
{
	/**
	 * Controller
	 *
	 * @var Controller
	 */
	public $Controller = null;

	/**
	 * Startup
	 *
	 * @param Controller $controller
	 */
	public function startup(Controller $controller)
	{
		parent::startup($controller);
		$this->Controller = $controller;
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
	public function install($siteUrl, $dbConfig, $adminUser = [], $baseUrl = '', $dbDataPattern = '')
	{
		if (!$dbDataPattern) {
			$dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
		}

		$result = true;

		// キャッシュ削除
		clearAllCache();

		// 一時フォルダ作成
		checkTmpFolders();

		if ($dbConfig['datasource'] == 'sqlite' || $dbConfig['datasource'] == 'csv') {
			switch($dbConfig['datasource']) {
				case 'sqlite':
					$dbFolderPath = APP . 'db' . DS . 'sqlite';
					break;
				case 'csv':
					$dbFolderPath = APP . 'db' . DS . 'csv';
					break;
			}
			$Folder = new Folder();
			if (!is_writable($dbFolderPath) && !$Folder->create($dbFolderPath, 0777)) {
				$this->log(__d('baser', 'データベースの保存フォルダの作成に失敗しました。db フォルダの書き込み権限を見なおしてください。'));
				$result = false;
			}
		}

		// SecritySaltの設定
		$securitySalt = $this->setSecuritySalt();
		$securityCipherSeed = $this->setSecurityCipherSeed();

		// インストールファイル作成
		if (!$this->createInstallFile($securitySalt, $securityCipherSeed, $siteUrl)) {
			$this->log(__d('baser', 'インストールファイル生成に失敗しました。設定フォルダの書き込み権限を見なおしてください。'));
			$result = false;
		}

		// データベース設定ファイル生成
		if (!$this->createDatabaseConfig($dbConfig)) {
			$this->log(__d('baser', 'データベースの設定ファイル生成に失敗しました。設定フォルダの書き込み権限を見なおしてください。'));
			$result = false;
		}

		// データベース初期化
		if (!$this->constructionDb($dbConfig, $dbDataPattern, Configure::read('BcApp.defaultAdminTheme'))) {
			$this->log(__d('baser', 'データベースの初期化に失敗しました。データベースの設定を見なおしてください。'));
			$result = false;
		}

		if ($adminUser) {
			// サイト基本設定登録
			if (!$this->setAdminEmail($adminUser['email'])) {
				$this->log(__d('baser', 'サイト基本設定への管理者メールアドレスの設定処理が失敗しました。データベースの設定を見なおしてください。'));
			}
			// ユーザー登録
			$adminUser['password_1'] = $adminUser['password'];
			$adminUser['password_2'] = $adminUser['password'];
			if (!$this->addDefaultUser($adminUser)) {
				$this->log(__d('baser', '初期ユーザーの作成に失敗しました。データベースの設定を見なおしてください。'));
				$result = false;
			}
		}

		// データベースの初期更新
		if (!$this->executeDefaultUpdates($dbConfig)) {
			$this->log(__d('baser', 'データベースのデータ更新に失敗しました。データベースの設定を見なおしてください。'));
			$result = false;
		}

		// コアプラグインのインストール
		if (!$this->installCorePlugin($dbConfig, $dbDataPattern)) {
			$this->log(__d('baser', 'コアプラグインのインストールに失敗しました。'));
			$result = false;
		}

		// テーマを配置
		if (!$this->deployTheme()) {
			$this->log(__d('baser', 'テーマの配置に失敗しました。テーマフォルダの書き込み権限を確認してください。'));
			$result = false;
		}

		// テーマに管理画面のアセットへのシンボリックリンクを作成する
		if (!$this->deployAdminAssets()) {
			$this->log(__d('baser', '管理システムのアセットファイルの配置に失敗しました。テーマフォルダの書き込み権限を確認してください。'));
		}

		// アップロード用初期フォルダを作成する
		if (!$this->createDefaultFiles()) {
			$this->log(__d('baser', 'アップロード用初期フォルダの作成に失敗しました。files フォルダの書き込み権限を確認してください。'));
			$result = false;
		}

		// エディタテンプレート用の画像を配置
		if (!$this->deployEditorTemplateImage()) {
			$this->log(__d('baser', 'エディタテンプレートイメージの配置に失敗しました。files フォルダの書き込み権限を確認してください。'));
			$result = false;
		}

		//SiteConfigを再設定
		loadSiteConfig();

		// ページファイルを生成
		$this->createPageTemplates();

		return $result;
	}

	/**
	 * コアプラグインをインストールする
	 *
	 * TODO 引数となる $dbDataPattern は、BcManager::installPlugin() で利用できる仕様となっていない
	 * @return bool
	 */
	public function installCorePlugin($dbConfig, $dbDataPattern)
	{
		$result = true;
		$corePlugins = Configure::read('BcApp.corePlugins');
		$this->connectDb($dbConfig, 'plugin');
		foreach($corePlugins as $corePlugin) {
			CakePlugin::load($corePlugin);
			if (!$this->installPlugin($corePlugin, $dbDataPattern)) {
				$this->log(sprintf(__d('baser', 'コアプラグイン %s のインストールに失敗しました。'), $corePlugin));
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * データベースに接続する
	 *
	 * @param array $config
	 * @return DboSource $db
	 */
	public function connectDb($config, $name = 'default')
	{

		if (!$datasource = $this->getDatasourceName($config['datasource'])) {
			return ConnectionManager::getDataSource($name);
		}
		$result = ConnectionManager::create($name, [
			'datasource' => $datasource,
			'persistent' => false,
			'host' => $config['host'],
			'port' => $config['port'],
			'login' => $config['login'],
			'password' => $config['password'],
			'database' => $config['database'],
			'schema' => $config['schema'],
			'prefix' => $config['prefix'],
			'encoding' => $config['encoding']]);
		if ($result) {
			return $result;
		} else {
			return ConnectionManager::getDataSource($name);
		}
	}

	/**
	 * datasource名を取得
	 *
	 * @param string datasource name.postgre.mysql.etc.
	 * @return string
	 */
	public function getDatasourceName($datasource = null)
	{
		$name = $datasource;
		switch($datasource) {
			case 'postgres' :
				$name = 'Database/BcPostgres';
				break;
			case 'mysql' :
				$name = 'Database/BcMysql';
				break;
			case 'sqlite' :
				$name = 'Database/BcSqlite';
				break;
			case 'csv' :
				$name = 'Database/BcCsv';
				break;
			default :
		}
		return $name;
	}

	/**
	 * 実際の設定用のDB名を取得する
	 *
	 * @param string $type
	 * @param string $name
	 * @return string
	 * @access    public
	 */
	public function getRealDbName($type, $name)
	{
		if (preg_match('/^\//', $name)) {
			return $name;
		}
		/* dbName */
		if (!empty($type) && !empty($name)) {
			if ($type == 'sqlite') {
				return APP . 'db' . DS . 'sqlite' . DS . $name . '.db';
			} elseif ($type == 'csv') {
				return APP . 'db' . DS . 'csv' . DS . $name;
			}
		}

		return $name;
	}

	/**
	 * テーマ用のページファイルを生成する
	 *
	 * @access    protected
	 */
	public function createPageTemplates()
	{
		ClassRegistry::flush();
		$Page = ClassRegistry::init('Page');
		$Page->searchIndexSaving = false;
		clearAllCache();
		$pages = $Page->find('all', ['conditions' => ['Content.alias_id' => null], 'recursive' => 0]);
		if ($pages) {
			foreach($pages as $page) {
				$Page->create($page);
				$Page->afterSave(true);
			}
		}
		return true;
	}

	/**
	 * データベースのデータに初期更新を行う
	 */
	public function executeDefaultUpdates($dbConfig)
	{
		$result = true;
		if (!$this->_updatePluginStatus($dbConfig)) {
			$this->log(__d('baser', 'プラグインの有効化に失敗しました。'));
			$result = false;
		}
		if (!$this->_updateContents()) {
			$this->log(__d('baser', 'コンテンツの更新に失敗しました。'));
			$result = false;
		}
		return $result;
	}

	/**
	 * コンテンツを更新する
	 * @return bool
	 */
	protected function _updateContents()
	{
		App::uses('Content', 'Model');
		$Content = new Content();
		$contents = $Content->find('all', ['recursive' => -1]);
		$result = true;
		foreach($contents as $content) {
			$content['Content']['created_date'] = date('Y-m-d H:i:s');
			if (!$Content->save($content, ['validation' => false, 'callbacks' => false])) {
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * プラグインのステータスを更新する
	 *
	 * @return boolean
	 * @access    protected
	 */
	protected function _updatePluginStatus($dbConfig)
	{
		$db = $this->_getDataSource('default', $dbConfig);
		$db->truncate('plugins');

		$version = getVersion();
		$Plugin = new Plugin();
		$corePlugins = Configure::read('BcApp.corePlugins');

		$result = true;
		$priority = intval($Plugin->getMax('priority')) + 1;
		foreach($corePlugins as $corePlugin) {
			$data = [];
			include BASER_PLUGINS . $corePlugin . DS . 'config.php';
			$data['Plugin']['name'] = $corePlugin;
			$data['Plugin']['title'] = $title;
			$data['Plugin']['version'] = $version;
			$data['Plugin']['status'] = true;
			$data['Plugin']['db_inited'] = false;
			$data['Plugin']['priority'] = $priority;
			$Plugin->create($data);
			if (!$Plugin->save()) {
				$result = false;
			}
			$priority++;
		}
		return $result;
	}

	/**
	 * サイト基本設定に管理用メールアドレスを登録する
	 *
	 * @param string $email
	 * @return boolean
	 * @access public
	 */
	public function setAdminEmail($email)
	{
		App::uses('SiteConfig', 'Model');
		$data['SiteConfig']['email'] = $email;
		$SiteConfig = new SiteConfig();
		return $SiteConfig->saveKeyValue($data);
	}

	/**
	 * 初期ユーザーを登録する
	 *
	 * @param array $user
	 * @return boolean
	 */
	public function addDefaultUser($user, $securitySalt = '')
	{
		if ($securitySalt) {
			Configure::write('Security.salt', $securitySalt);
		}

		$user += [
			'real_name_1' => $user['name']
		];
		$user = array_merge([
			'name' => '',
			'real_name_1' => '',
			'email' => '',
			'user_group_id' => 1,
			'password_1' => '',
			'password_2' => ''
		], $user);

		/** 2016/09/21 gondoh
		 *  Consoleから動作させた場合ClassRegistryからインスタンスを取得すると
		 *  動的生成されたAppModelを利用してしまうため明示的にnewする。
		 */
		$User = new User();

		$user['password'] = $user['password_1'];
		$User->create($user);

		return $User->save();
	}

	/**
	 * データベース設定ファイル[database.php]を保存する
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function createDatabaseConfig($options = [])
	{
		if (!is_writable(APP . 'Config' . DS)) {
			return false;
		}

		$options = array_merge([
			'datasource' => '',
			'host' => 'localhost',
			'port' => '',
			'login' => 'dummy',
			'password' => 'dummy',
			'database' => 'dummy',
			'prefix' => '',
			'schema' => '',
			'encoding' => 'utf8'
		], $options);

		// 入力された文字列よりPHPプログラムファイルを生成するため'(シングルクオート)をサニタイズ
		foreach($options as $key => $option) {
			$options[$key] = addcslashes($option, '\'\\');
		}

		extract($options);

		$datasource = $this->getDatasourceName($datasource);

		$dbfilename = APP . 'Config' . DS . 'database.php';
		$file = new File($dbfilename);

		if ($file !== false) {

			if ($file->exists()) {
				$file->delete();
			}

			$file->create();
			$file->open('w', true);
			$file->write("<?php\n");
			$file->write("//\n");
			$file->write("// Database Configuration File created by baserCMS Installation\n");
			$file->write("//\n");

			$file->write("class DATABASE_CONFIG {\n");
			$file->write('public $default = array(' . "\n");
			$file->write("\t'datasource' => '" . $datasource . "',\n");
			$file->write("\t'persistent' => false,\n");
			$file->write("\t'host' => '" . $host . "',\n");
			$file->write("\t'port' => '" . $port . "',\n");
			$file->write("\t'login' => '" . $login . "',\n");
			$file->write("\t'password' => '" . $password . "',\n");
			$file->write("\t'database' => '" . $database . "',\n");
			$file->write("\t'schema' => '" . $schema . "',\n");
			$file->write("\t'prefix' => '" . $prefix . "',\n");
			$file->write("\t'encoding' => '" . $encoding . "'\n");
			$file->write(");\n");

			$file->write('public $test = array(' . "\n");
			$file->write("\t'datasource' => '" . $datasource . "',\n");
			$file->write("\t'persistent' => false,\n");
			$file->write("\t'host' => '" . $host . "',\n");
			$file->write("\t'port' => '" . $port . "',\n");
			$file->write("\t'login' => '" . $login . "',\n");
			$file->write("\t'password' => '" . $password . "',\n");
			$file->write("\t'database' => '" . $database . "',\n");
			$file->write("\t'schema' => '" . $schema . "',\n");
			$file->write("\t'prefix' => '" . $prefix . Configure::read('BcEnv.testDbPrefix') . "',\n");
			$file->write("\t'encoding' => '" . $encoding . "'\n");
			$file->write(");\n");
			$file->write("}\n");

			$file->close();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * インストール設定ファイルを生成する
	 *
	 * @return boolean
	 */
	public function createInstallFile($securitySalt, $secrityCipherSeed, $siteUrl = "")
	{
		$installFileName = APP . 'Config' . DS . 'install.php';

		if (!$siteUrl) {
			$siteUrl = siteUrl();
		}
		$installCoreData = ["<?php",
			"Configure::write('Security.salt', '{$securitySalt}');",
			"Configure::write('Security.cipherSeed', '{$secrityCipherSeed}');",
			"Configure::write('Cache.disable', false);",
			"Configure::write('Cache.check', true);",
			"Configure::write('BcEnv.siteUrl', '{$siteUrl}');",
			"Configure::write('BcEnv.sslUrl', '');",
			"Configure::write('BcEnv.mainDomain', '');",
			"Configure::write('BcApp.adminSsl', false);",
			"Configure::write('BcApp.allowedPhpOtherThanAdmins', false);",
			"Cache::config('default', array('engine' => 'File'));",
			"Configure::write('debug', 0);"
		];
		if (file_put_contents($installFileName, implode("\n", $installCoreData))) {
			return chmod($installFileName, 0666);
		} else {
			return false;
		}
	}

	/**
	 * セキュリティ用のキーを生成する
	 *
	 * @param int $length
	 * @return string キー
	 * @access    protected
	 */
	public function setSecuritySalt($length = 40)
	{
		$keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$randkey = "";
		for($i = 0; $i < $length; $i++) {
			$randkey .= $keyset[mt_rand(0, strlen($keyset) - 1)];
		}
		Configure::write('Security.salt', $randkey);
		return $randkey;
	}

	/**
	 * セキュリティ用の数字キーを生成する
	 *
	 * @param int $length
	 * @return string 数字キー
	 * @access    public
	 */
	public function setSecurityCipherSeed($length = 29)
	{
		$keyset = "0123456789";
		$randkey = "";
		for($i = 0; $i < $length; $i++) {
			$randkey .= $keyset[mt_rand(0, strlen($keyset) - 1)];
		}
		Configure::write('Security.cipherSeed', $randkey);
		return $randkey;
	}

	/**
	 * baserCMSコアのデータベースを構築する
	 *
	 * @param array $dbConfig データベース設定名
	 * @param string $dbDataPattern データパターン
	 * @return boolean
	 */
	public function constructionDb($dbConfig, $dbDataPattern = '', $adminTheme = '')
	{

		$coreExcludes = ['users', 'dblogs', 'plugins'];

		if (!$dbDataPattern) {
			$dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
		}

		if (strpos($dbDataPattern, '.') === false) {
			$this->log(__d('baser', 'データパターンの形式が不正です。'));
			return false;
		}

		list($theme, $pattern) = explode('.', $dbDataPattern);

		if (!$this->constructionTable('Core', 'default', $dbConfig)) {
			$this->log(__d('baser', 'コアテーブルの構築に失敗しました。'));
			return false;
		}

		if (!$this->loadDefaultDataPattern('default', $dbConfig, $pattern, $theme, 'core', $coreExcludes)) {
			$this->log(__d('baser', 'コアの初期データのロードに失敗しました。'));
			return false;
		}

		if (!$this->initSystemData($dbConfig, ['adminTheme' => $adminTheme])) {
			$this->log(__d('baser', 'システムデータの初期化に失敗しました。'));
			return false;
		}

		$Db = $this->_getDataSource();
		if ($Db->config['datasource'] == 'Database/BcPostgres') {
			$Db->updateSequence();
		}

		return true;
	}

	/**
	 * 全ての初期データセットのリストを取得する
	 *
	 * @return array
	 */
	public function getAllDefaultDataPatterns()
	{
		$patterns = [];

		// コア
		$patterns = $this->getDefaultDataPatterns();

		// コアテーマ
		$Folder = new Folder(BASER_CONFIGS . 'theme');
		$files = $Folder->read(true, true);
		foreach($files[0] as $theme) {
			if ($theme != 'empty') {
				$patterns = array_merge($patterns, $this->getDefaultDataPatterns($theme));
			}
		}

		// 外部テーマ
		$Folder = new Folder(BASER_THEMES);
		$files = $Folder->read(true, true, false);
		foreach($files[0] as $theme) {
			if ($theme != 'empty') {
				$patterns = array_merge($patterns, $this->getDefaultDataPatterns($theme));
			}
		}

		return $patterns;
	}

	/**
	 * 初期データのセットを取得する
	 *
	 * @param string $theme
	 * @param array $options
	 * @return array
	 */
	public function getDefaultDataPatterns($theme = 'core', $options = [])
	{
		$options = array_merge(['useTitle' => true], $options);
		extract($options);

		$themePath = $dataPath = $title = '';
		$dataPath = dirname(BcUtil::getDefaultDataPath('Core', $theme));

		if ($theme != 'core' && $dataPath == dirname(BcUtil::getDefaultDataPath('Core'))) {
			return [];
		}

		if (is_dir(BASER_THEMES . $theme)) {
			$themePath = BASER_THEMES . $theme . DS;
		} elseif (is_dir(BASER_CONFIGS . 'theme' . DS . $theme)) {
			$themePath = BASER_CONFIGS . 'theme' . DS . $theme . DS;
		}

		if ($themePath) {
			if (file_exists($themePath . 'config.php')) {
				include $themePath . 'config.php';
			}
		} else {
			$title = __d('baser', 'コア');
		}

		if (!$title) {
			$title = $theme;
		}

		$patterns = [];
		$Folder = new Folder($dataPath);
		$files = $Folder->read(true, true);
		if ($files[0]) {
			foreach($files[0] as $pattern) {
				if ($useTitle) {
					$patternName = $title . ' ( ' . $pattern . ' )';
				} else {
					$patternName = $pattern;
				}
				$patterns[$theme . '.' . $pattern] = $patternName;
			}
		}
		return $patterns;
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
	public function loadDefaultDataPattern($dbConfigKeyName, $dbConfig, $pattern, $theme = 'core', $plugin = 'core', $excludes = [])
	{
		$db = $this->_getDataSource($dbConfigKeyName, $dbConfig);

		$path = BcUtil::getDefaultDataPath($plugin, $theme, $pattern);

		if (!$path) {
			return false;
		}

		$corePath = BcUtil::getDefaultDataPath($plugin, 'core', 'default');

		$targetTables = [];
		if ($corePath) {
			$Folder = new Folder($corePath);
			$files = $Folder->read(true, true);
			$targetTables = $files[1];
		}
		$Folder = new Folder($path);
		$files = $Folder->read(true, true, true);
		if (!$targetTables) {
			$targetTables = $files[1];
		}

		$result = true;

		foreach($targetTables as $targetTable) {
			$targetTable = basename($targetTable, '.csv');
			$loaded = false;
			if (!in_array($targetTable, $excludes)) {
				// 初期データ投入
				foreach($files[1] as $file) {
					if (!preg_match('/\.csv$/', $file)) {
						continue;
					}
					$table = basename($file, '.csv');
					if ($table == $targetTable) {
						if (!$db->loadCsv(['path' => $file, 'encoding' => 'auto'])) {
							$this->log(sprintf(__d('baser', '%s の読み込みに失敗。'), $file));
							$result = false;
						} else {
							$loaded = true;
							break;
						}
					}
				}
				// 存在しなかった場合は、コアのファイルを読み込む
				if (!$loaded && $corePath) {
					if (!$db->loadCsv(['path' => $corePath . DS . $targetTable . '.csv', 'encoding' => 'auto'])) {
						$this->log(sprintf(__d('baser', '%s の読み込みに失敗。'), $corePath . DS . $targetTable));
						$result = false;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * システムデータを初期化する
	 *
	 * @param string $dbConfigKeyName
	 * @param array $dbConfig
	 */
	public function initSystemData($dbConfig = null, $options = [])
	{

		$options = array_merge([
			'excludeUsers' => false,
			'adminTheme' => ''
		], $options);

		$db = $this->_getDataSource('default', $dbConfig);
		$corePath = BASER_CONFIGS . 'data' . DS . 'default';
		$result = true;

		/* user_groupsの初期データをチェック＆設定 */
		$UserGroup = ClassRegistry::init('UserGroup');
		if (!$UserGroup->find('count', ['UserGroup.name' => 'admins'])) {
			$userGroups = $db->loadCsvToArray($corePath . DS . 'user_groups.csv', 'SJIS');
			foreach($userGroups as $userGroup) {
				if ($userGroup['name'] == 'admins') {
					$UserGroup->save($userGroup);
					break;
				}
			}
		}

		/* users は全てのユーザーを削除 */
		//======================================================================
		// ユーザーグループを新しく読み込んだ場合にデータの整合性がとれない可能性がある為
		//======================================================================
		if (!$options['excludeUsers']) {
			if (!$db->truncate('users')) {
				$this->log(__d('baser', 'users テーブルの初期化に失敗。'));
				$result = false;
			}
		}

		/* site_configs の初期データをチェック＆設定 */
		$SiteConfig = ClassRegistry::init('SiteConfig');
		if (!$SiteConfig->updateAll(['SiteConfig.value' => null], ['SiteConfig.name' => 'email']) ||
			!$SiteConfig->updateAll(['SiteConfig.value' => null], ['SiteConfig.name' => 'google_analytics_id']) ||
			!$SiteConfig->updateAll(['SiteConfig.value' => true], ['SiteConfig.name' => 'first_access']) ||
			!$SiteConfig->updateAll(['SiteConfig.value' => "'" . $options['adminTheme'] . "'"], ['SiteConfig.name' => 'admin_theme']) ||
			!$SiteConfig->deleteAll(['SiteConfig.name' => 'version'], false)) {
			$this->log(__d('baser', 'site_configs テーブルの初期化に失敗'));
			$result = false;
		}

		return $result;
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
	public function constructionTable($plugin, $dbConfigKeyName = 'default', $dbConfig = null)
	{

		$db = $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$datasource = strtolower(preg_replace('/^Database\/Bc/', '', $db->config['datasource']));

		if (@!$db->connected && $datasource != 'csv') {
			return false;
		} elseif ($datasource == 'csv') {
			// CSVの場合はフォルダを作成する
			$Folder = new Folder($db->config['database'], true, 0777);
		} elseif ($datasource == 'sqlite') {
			$db->connect();
			chmod($db->config['database'], 0666);
		}

		$path = BcUtil::getSchemaPath($plugin);

		// DB構築
		$Folder = new Folder($path);
		$files = $Folder->read(true, true, true);
		if (isset($files[1])) {
			foreach($files[1] as $file) {

				if (!preg_match('/\.php$/', $file)) {
					continue;
				}
				if (!$db->createTableBySchema(['path' => $file])) {
					return false;
				}
			}
		}

		return true;
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
	public function resetAllTables($dbConfig = null, $excludes = [])
	{
		$result = true;
		if (!$this->resetTables('default', $dbConfig, 'core', $excludes)) {
			$result = false;
		}
		$Plugin = ClassRegistry::init('Plugin');
		$plugins = $Plugin->find('all', ['conditions' => ['Plugin.status' => true]]);
		$plugins = Hash::extract($plugins, '{n}.Plugin.name');
		foreach($plugins as $plugin) {
			if (!$this->resetTables('default', $dbConfig, $plugin, $excludes)) {
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * テーブルをリセットする
	 *
	 * @param type $dbConfigKeyName
	 * @param type $dbConfig
	 * @return boolean
	 */
	public function resetTables($dbConfigKeyName = 'default', $dbConfig = null, $plugin = 'core', $excludes = [])
	{
		$db = $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$dbConfig = $db->config;
		$db->reconnect();
		$sources = $db->listSources();
		$result = true;

		$pluginTables = [];
		if ($plugin != 'core') {
			$path = BcUtil::getSchemaPath($plugin);
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, false);
			if (empty($files[1])) {
				return true;
			}
			foreach($files[1] as $file) {
				if (preg_match('/\.php$/', $file)) {
					$pluginTables[] = preg_replace('/\.php/', '', $file);
				}
			}
		}

		foreach($sources as $source) {
			if (preg_match("/^" . $dbConfig['prefix'] . "([^_].+)$/", $source, $matches)) {
				$table = $matches[1];
				if ($plugin == 'core') {
					if (in_array($table, $pluginTables)) {
						continue;
					}
				} else {
					if (!in_array($table, $pluginTables)) {
						continue;
					}
				}
				if (!in_array($table, $excludes)) {
					if (!$db->truncate($table)) {
						$result = false;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * テーブルを削除する
	 *
	 * @param string $dbConfigKeyName
	 * @param array $dbConfig
	 * @return boolean
	 * TODO 処理を DboSource に移動する
	 * TODO コアのテーブルを削除する際、プレフィックスだけでは、プラグインを識別できないので、プラグインのテーブルも削除されてしまう。
	 *        その為、プラグインのテーブルを削除しようとすると存在しない為、Excerptionが発生してしまい。処理が停止してしまうので、
	 *        try で実行し、catch はスルーしている。
	 */
	public function deleteTables($dbConfigKeyName = 'default', $dbConfig = null)
	{
		$db = $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$dbConfig = $db->config;

		/* 削除実行 */
		// TODO schemaを有効活用すればここはスッキリしそうだが見送り
		$datasource = strtolower(preg_replace('/^Database\/Bc/', '', $db->config['datasource']));
		switch($datasource) {
			case 'mysql':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if (preg_match("/^" . $dbConfig['prefix'] . "([^_].+)$/", $source)) {
						$sql = 'DROP TABLE ' . $source;
						try {
							$db->execute($sql);
						} catch (Exception $e) {
						}
					}
				}
				break;

			case 'postgres':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if (preg_match("/^" . $dbConfig['prefix'] . "([^_].+)$/", $source)) {
						$sql = 'DROP TABLE ' . $source;
						try {
							$db->execute($sql);
						} catch (Exception $e) {
						}
					}
				}
				// シーケンスも削除
				$sql = "SELECT sequence_name FROM INFORMATION_SCHEMA.sequences WHERE sequence_schema = '{$dbConfig['schema']}';";
				$sequences = [];
				try {
					$sequences = $db->query($sql);
				} catch (Exception $e) {
				}
				if ($sequences) {
					$sequences = Hash::extract($sequences, '0.sequence_name');
					foreach($sequences as $sequence) {
						if (preg_match("/^" . $dbConfig['prefix'] . "([^_].+)$/", $sequence)) {
							$sql = 'DROP SEQUENCE ' . $sequence;
							try {
								$db->execute($sql);
							} catch (Exception $e) {
							}
						}
					}
				}
				break;

			case 'sqlite':
				@unlink($dbConfig['database']);
				break;

			case 'csv':
				$folder = new Folder($dbConfig['database']);
				$files = $folder->read(true, true, true);
				foreach($files[1] as $file) {
					if (basename($file) != 'empty') {
						@unlink($file);
					}
				}
				break;
		}
		return true;
	}

	/**
	 * データソースを取得する
	 *
	 * @param string $configKeyName
	 * @param array $dbConfig
	 * @return DataSource
	 */
	protected function _getDataSource($dbConfigKeyName = 'default', $dbConfig = null)
	{
		if ($dbConfig) {
			$dbConfig['datasource'] = $this->getDatasourceName($dbConfig['datasource']);
			$db = ConnectionManager::create($dbConfigKeyName, $dbConfig);
			if (!$db) {
				$db = ConnectionManager::getDataSource($dbConfigKeyName);
			}
		} else {
			$db = ConnectionManager::getDataSource($dbConfigKeyName);
		}

		return $db;
	}

	/**
	 * テーマを配置する
	 *
	 * @param string $theme
	 * @return boolean
	 */
	public function deployTheme($theme = null)
	{

		$this->resetTheme();
		$Folder = new Folder(BASER_CONFIGS . 'theme');

		if ($theme) {
			if (is_array($theme)) {
				$sources = $theme;
			} else {
				$sources = [$theme];
			}
		} else {
			$files = $Folder->read();
			$sources = $files[0];
		}

		$result = true;
		foreach($sources as $theme) {
			$targetPath = WWW_ROOT . 'theme' . DS . $theme;
			$sourcePath = BASER_CONFIGS . 'theme' . DS . $theme;
			if ($Folder->copy(['to' => $targetPath, 'from' => $sourcePath, 'mode' => 0777, 'skip' => ['_notes']])) {
				if (!$Folder->create($targetPath . DS . 'Pages', 0777)) {
					$result = false;
				}
			} else {
				$result = false;
			}
		}
		$Folder = null;
		return $result;
	}

	/**
	 * エディタテンプレート用のアイコン画像をデプロイ
	 *
	 * @return boolean
	 */
	public function deployEditorTemplateImage()
	{
		$path = WWW_ROOT . 'files' . DS . 'editor' . DS;
		if (!is_dir($path)) {
			$Folder = new Folder();
			$Folder->create($path, 0777);
		}

		$src = BASER_WEBROOT . 'img' . DS . 'admin' . DS . 'ckeditor' . DS;
		$Folder = new Folder($src);
		$files = $Folder->read(true, true);
		$result = true;
		if (!empty($files[1])) {
			foreach($files[1] as $file) {
				if (copy($src . $file, $path . $file)) {
					@chmod($path . $file, 0666);
				} else {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * アップロード用初期フォルダを作成する
	 */
	public function createDefaultFiles()
	{
		$dirs = ['blog', 'editor', 'theme_configs'];
		$path = WWW_ROOT . 'files' . DS;
		$Folder = new Folder();

		$result = true;
		foreach($dirs as $dir) {
			if (!is_dir($path . $dir)) {
				if (!$Folder->create($path . $dir, 0777)) {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * 設定ファイルをリセットする
	 *
	 * @return boolean
	 */
	public function resetSetting()
	{
		$result = true;
		if (file_exists(APP . 'Config' . DS . 'database.php')) {
			if (!unlink(APP . 'Config' . DS . 'database.php')) {
				$result = false;
			}
		}
		if (file_exists(APP . 'Config' . DS . 'install.php')) {
			if (!unlink(APP . 'Config' . DS . 'install.php')) {
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * files フォルダを初期化する
	 *
	 * @return boolean
	 */
	public function resetFiles()
	{
		return $this->resetEmptyFolder(WWW_ROOT . 'files');
	}

	/**
	 * 管理画面用のアセットフォルダ（img / js / css）を初期化する
	 *
	 * @return boolean
	 */
	public function resetAdminAssets()
	{
		$paths = [
			WWW_ROOT . 'img' . DS . 'admin',
			WWW_ROOT . 'css' . DS . 'admin',
			WWW_ROOT . 'js' . DS . 'admin'
		];
		$result = true;
		foreach($paths as $path) {
			if (is_dir($path)) {
				$Folder = new Folder($path);
				if (!$Folder->delete()) {
					$result = false;
				}
				$Folder = null;
			}
		}
		return $result;
	}

	/**
	 * empty ファイルを梱包したフォルダをリセットする
	 *
	 * empty ファイルを残して内包するファイルとフォルダを全て削除する
	 *
	 * @param string $path
	 * @return boolean
	 */
	public function resetEmptyFolder($path)
	{
		$result = true;
		$Folder = new Folder($path);
		$files = $Folder->read(true, true, true);
		$Folder = null;
		if (!empty($files[0])) {
			foreach($files[0] as $file) {
				$Folder = new Folder();
				if (!$Folder->delete($file)) {
					$result = false;
				}
				$Folder = null;
			}
		}
		if (!empty($files[1])) {
			foreach($files[1] as $file) {
				if (basename($file) != 'empty') {
					$Folder = new Folder();
					if (!$Folder->delete($file)) {
						$result = false;
					}
					$Folder = null;
				}
			}
		}
		return $result;
	}

	/**
	 * baserCMSをリセットする
	 *
	 * @param array $dbConfig
	 */
	public function reset($dbConfig)
	{
		$result = true;

		if (BC_INSTALLED) {
			// 設定ファイルを初期化
			if (!$this->resetSetting()) {
				$result = false;
				$this->log(__d('baser', '設定ファイルを正常に初期化できませんでした。'));
			}
			// テーブルを全て削除
			if (!$this->deleteTables('default', $dbConfig)) {
				$result = false;
				$this->log(__d('baser', 'データベースを正常に初期化できませんでした。'));
			}
		}

		// テーマのテンプレートを初期化
		if (!$this->resetTheme()) {
			$result = false;
			$this->log(__d('baser', 'テーマフォルダを初期化できませんでした。'));
		}

		// 固定ページテンプレートを初期化
		if (!$this->resetPages()) {
			$result = false;
			$this->log(__d('baser', '固定ページテンプレートを初期化できませんでした。'));
		}

		// files フォルダの初期化
		if (!$this->resetFiles()) {
			$result = false;
			$this->log(__d('baser', 'files フォルダを初期化できませんでした。'));
		}

		// files フォルダの初期化
		if (!$this->resetAdminAssets()) {
			$result = false;
			$this->log(__d('baser', 'img / css / js フォルダを初期化できませんでした。'));
		}

		ClassRegistry::flush();
		clearAllCache();

		return $result;
	}

	/**
	 * テーマリセットする
	 *
	 * @return bool
	 */
	public function resetTheme()
	{
		$Folder = new Folder(BASER_CONFIGS . 'theme');
		$sources = $Folder->read()[0];
		$result = true;
		foreach($sources as $theme) {
			$targetPath = WWW_ROOT . 'theme' . DS . $theme;
			if (is_dir($targetPath)) {
				if (!$Folder->delete($targetPath)) {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * 固定ページテンプレートをリセットする
	 *
	 * @return bool
	 */
	public function resetPages()
	{
		$Folder = new Folder(APP . 'View' . DS . 'Pages');
		$files = $Folder->read(true, true, true);
		$result = true;
		foreach($files[0] as $file) {
			if (!$Folder->delete($file)) {
				$result = false;
			}
		}
		foreach($files[1] as $file) {
			if (basename($file) != 'empty') {
				if (!@unlink($file)) {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * インストール設定を書き換える
	 *
	 * @param string $key
	 * @param string $value
	 * @return    boolean
	 * @access    public
	 */
	public function setInstallSetting($key, $value)
	{
		/* install.php の編集 */
		$setting = "Configure::write('" . $key . "', " . $value . ");\n";
		$key = str_replace('.', '\.', $key);
		$pattern = '/Configure\:\:write[\s]*\([\s]*\'' . $key . '\'[\s]*,[\s]*([^\s]*)[\s]*\);(\n|)/is';
		$file = new File(APP . 'Config' . DS . 'install.php');
		if (file_exists(APP . 'Config' . DS . 'install.php')) {
			$data = $file->read();
		} else {
			$data = "<?php\n";
		}
		if (preg_match($pattern, $data)) {
			$data = preg_replace($pattern, $setting, $data);
		} else {
			$data = $data . "\n" . $setting;
		}
		$return = $file->write($data);
		$file->close();
		return $return;
	}

	/**
	 * 環境チェック
	 *
	 * @return array
	 */
	public function checkEnv()
	{
		if (function_exists('apache_get_modules')) {
			$rewriteInstalled = in_array('mod_rewrite', apache_get_modules());
		} else {
			$rewriteInstalled = -1;
		}

		$status = [
			'encoding' => mb_internal_encoding(),
			'phpVersion' => phpversion(),
			'phpMemory' => $this->_getMemoryLimit(),
			'safeModeOff' => !ini_get('safe_mode'),
			'configDirWritable' => is_writable(APP . 'Config' . DS),
			'pluginDirWritable' => is_writable(APP . 'Plugin' . DS),
			'themeDirWritable' => is_writable(WWW_ROOT . 'theme'),
			'filesDirWritable' => is_writable(WWW_ROOT . 'files'),
			'imgDirWritable' => is_writable(WWW_ROOT . 'img'),
			'jsDirWritable' => is_writable(WWW_ROOT . 'js'),
			'cssDirWritable' => is_writable(WWW_ROOT . 'css'),
			'imgAdminDirExists' => is_dir(WWW_ROOT . 'img' . DS . 'admin'),
			'jsAdminDirExists' => is_dir(WWW_ROOT . 'js' . DS . 'admin'),
			'cssAdminDirExists' => is_dir(WWW_ROOT . 'css' . DS . 'admin'),
			'tmpDirWritable' => is_writable(TMP),
			'pagesDirWritable' => is_writable(APP . 'View' . DS . 'Pages'),
			'dbDirWritable' => is_writable(APP . 'db'),
			'phpActualVersion' => preg_replace('/[a-z-]/', '', phpversion()),
			'phpGd' => extension_loaded('gd'),
			'phpPdo' => extension_loaded('pdo'),
			'phpXml' => extension_loaded('xml'),
			'phpZip' => extension_loaded('zip'),
			'apacheRewrite' => $rewriteInstalled,
		];
		$check = [
			'encodingOk' => (preg_match('/UTF-8/i', $status['encoding'])? true : false),
			'gdOk' => $status['phpGd'],
			'pdoOk' => $status['phpPdo'],
			'xmlOk' => $status['phpXml'],
			'zipOk' => $status['phpZip'],
			'phpVersionOk' => version_compare(preg_replace('/[a-z-]/', '', $status['phpVersion']), Configure::read('BcRequire.phpVersion'), '>='),
			'phpMemoryOk' => ((($status['phpMemory'] >= Configure::read('BcRequire.phpMemory')) || $status['phpMemory'] == -1) === true)
		];

		if (!$status['configDirWritable']) {
			@chmod(APP . 'Config' . DS, 0777);
			$status['configDirWritable'] = is_writable(APP . 'Config' . DS);
		}
		if (!$status['pluginDirWritable']) {
			@chmod(APP . 'Plugin' . DS, 0777);
			$status['pluginDirWritable'] = is_writable(APP . 'Plugin' . DS);
		}
		if (!$status['themeDirWritable']) {
			@chmod(WWW_ROOT . 'theme', 0777);
			$status['themeDirWritable'] = is_writable(WWW_ROOT . 'theme');
		}
		if (!$status['filesDirWritable']) {
			@chmod(WWW_ROOT . 'files', 0777);
			$status['filesDirWritable'] = is_writable(WWW_ROOT . 'files');
		}
		if (!$status['imgDirWritable']) {
			@chmod(WWW_ROOT . 'img', 0777);
			$status['imgDirWritable'] = is_writable(WWW_ROOT . 'img');
		}
		if (!$status['cssDirWritable']) {
			@chmod(WWW_ROOT . 'css', 0777);
			$status['cssDirWritable'] = is_writable(WWW_ROOT . 'css');
		}
		if (!$status['jsDirWritable']) {
			@chmod(WWW_ROOT . 'js', 0777);
			$status['jsDirWritable'] = is_writable(WWW_ROOT . 'js');
		}
		if (!$status['tmpDirWritable']) {
			@chmod(TMP, 0777);
			$status['tmpDirWritable'] = is_writable(TMP);
		}
		if (!$status['dbDirWritable']) {
			@chmod(APP . 'db', 0777);
			$status['dbDirWritable'] = is_writable(APP . 'db');
		}

		return $status + $check;
	}

	/**
	 * memory_limit を取得する
	 * @return int
	 */
	protected function _getMemoryLimit ()
	{
		$size = ini_get('memory_limit');
		switch (substr ($size, -1)) {
			case 'M': case 'm': return (int) $size;
			case 'G': case 'g': return (int) $size * 1024;
			default: return (int) $size;
		}
	}

	/**
	 * DB接続チェック
	 *
	 * @param string[] $config
	 *   'datasource' 'MySQL' or 'Postgres' or 'SQLite' or 'CSV'
	 *   'database' データベース名 SQLiteの場合はファイルパス CSVの場合はディレクトリへのパス
	 *   'host' テキストDB or localhostの場合は不要
	 *   'port' 接続ポート テキストDBの場合は不要
	 *   'login' 接続ユーザ名 テキストDBの場合は不要
	 *   'password' 接続パスワード テキストDBの場合は不要
	 *
	 * @return boolean
	 * @throws PDOException
	 * @throws Exception
	 */
	public function checkDbConnection($config)
	{
		$datasource = Hash::get($config, 'datasource');
		$database = Hash::get($config, 'database');
		$host = Hash::get($config, 'host');
		$port = Hash::get($config, 'port');
		$login = Hash::get($config, 'login');
		$password = Hash::get($config, 'password');

		$datasource = strtolower($datasource);

		try {
			if ($datasource == 'mysql') {
				$dsn = "mysql:dbname={$database}";
				if ($host) {
					$dsn .= ";host={$host}";
				}
				if ($port) {
					$dsn .= ";port={$port}";
				}
				$pdo = new PDO($dsn, $login, $password);
			} elseif ($datasource == 'postgres') {
				$dsn = "pgsql:dbname={$database}";
				if ($host) {
					$dsn .= ";host={$host}";
				}
				if ($port) {
					$dsn .= ";port={$port}";
				}
				$pdo = new PDO($dsn, $login, $password);
			} elseif ($datasource == 'sqlite') {
				// すでにある場合
				if (file_exists($database)) {
					if (!is_writable($database)) {
						throw new Exception(__d('baser', "データベースファイルに書き込み権限がありません。"));
					}
					// ない場合
				} else {
					if (!is_writable(dirname($database))) {
						throw new Exception(__d('baser', 'データベースの保存フォルダに書き込み権限がありません。'));
					}
				}
				$dsn = "sqlite:" . $database;
				$pdo = new PDO($dsn);
			} elseif ($datasource == 'csv') {
				if (is_writable($database)) {
					return true;
				}
				throw new Exception(__d('baser', 'データベースの保存フォルダに書き込み権限がありません。'));
			} else {
				// ドライバが見つからない
				throw new Exception(__d('baser', 'ドライバが見つかりません Driver is not defined.(MySQL|Postgres|SQLite|CSV)'));
			}
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}

		// 接続できたよ
		if ($pdo) {
			// disconnect
			unset($pdo);
			return true;
		}
	}

	/**
	 * サイトルートに管理システム用アセットを配置する
	 *
	 * @return boolean
	 */
	public function deployAdminAssets()
	{
		$adminTheme = Configure::read('BcSite.admin_theme');
		$viewPath = WWW_ROOT;
		$baserWebroot = BASER_WEBROOT;
		if ($adminTheme) {
			if (is_dir(BASER_THEMES . $adminTheme)) {
				return true;
			} elseif (is_dir(BASER_VIEWS . 'Themed' . DS . $adminTheme)) {
				$baserWebroot = BASER_VIEWS . 'Themed' . DS . $adminTheme . DS;
			}
		}
		$adminCss = $baserWebroot . 'css' . DS . 'admin';
		$adminJs = $baserWebroot . 'js' . DS . 'admin';
		$adminImg = $baserWebroot . 'img' . DS . 'admin';
		$adminFonts = $baserWebroot . 'fonts' . DS . 'admin';
		$css = $viewPath . 'css' . DS . 'admin';
		$js = $viewPath . 'js' . DS . 'admin';
		$img = $viewPath . 'img' . DS . 'admin';
		$fonts = $viewPath . 'fonts' . DS . 'admin';
		$result = true;
		$Folder = new Folder();
		if (is_dir($adminCss)) {
			if (!$Folder->copy([
				'from' => $adminCss,
				'to' => $css,
				'mode' => 0777
			])) {
				$result = false;
			}
		}
		if (is_dir($adminJs)) {
			if (!$Folder->copy([
				'from' => $adminJs,
				'to' => $js,
				'mode' => 0777
			])) {
				$result = false;
			}
		}
		if (is_dir($adminImg)) {
			if (!$Folder->copy([
				'from' => $adminImg,
				'to' => $img,
				'mode' => 0777
			])) {
				$result = false;
			}
		}
		if (is_dir($adminFonts)) {
			if (!$Folder->copy([
				'from' => $adminFonts,
				'to' => $fonts,
				'mode' => 0777
			])) {
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * サイトルートの管理システム用アセットを削除する
	 *
	 * @return bool
	 */
	public function deleteAdminAssets()
	{
		$viewPath = WWW_ROOT;
		$css = $viewPath . 'css' . DS . 'admin';
		$js = $viewPath . 'js' . DS . 'admin';
		$img = $viewPath . 'img' . DS . 'admin';
		$fonts = $viewPath . 'fonts' . DS . 'admin';
		$result = true;
		$Folder = new Folder();
		if (!$Folder->delete($css)) {
			$result = false;
		}
		if (!$Folder->delete($js)) {
			$result = false;
		}
		if (!$Folder->delete($img)) {
			$result = false;
		}
		if (!$Folder->delete($fonts)) {
			$result = false;
		}
		return $result;
	}

	/**
	 * プラグインをインストールする
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function installPlugin($name, $dbDataPattern = '')
	{
		clearAllCache();

		$paths = App::path('Plugin');
		$exists = false;
		foreach($paths as $path) {
			if (file_exists($path . $name)) {
				$exists = true;
				break;
			}
		}

		if (!$exists) {
			return false;
		}

		$this->Plugin = ClassRegistry::init('Plugin');
		$data = $this->Plugin->find('first', ['conditions' => ['name' => $name]]);
		$title = '';

		if (empty($data['Plugin']['db_inited'])) {
			$initPath = $path . $name . DS . 'Config' . DS . 'init.php';
			if (file_exists($initPath)) {
				$this->initPlugin($initPath, $dbDataPattern);
			}
		}
		$configPath = $path . $name . DS . 'config.php';
		if (file_exists($configPath)) {
			include $configPath;
		}

		if (empty($title)) {
			if (!empty($data['Plugin']['title'])) {
				$title = $data['Plugin']['title'];
			} else {
				$title = $name;
			}
		}

		if ($data) {
			// 既にインストールデータが存在する場合は、DBのバージョンは変更しない
			$data = array_merge($data['Plugin'], [
				'name' => $name,
				'title' => $title,
				'status' => true,
				'db_inited' => true
			]);
			$this->Plugin->set($data);
		} else {
			$corePlugins = Configure::read('BcApp.corePlugins');
			if (in_array($name, $corePlugins)) {
				$version = getVersion();
			} else {
				$version = getVersion($name);
			}

			$priority = intval($this->Plugin->getMax('priority')) + 1;
			$data = ['Plugin' => [
				'name' => $name,
				'title' => $title,
				'status' => true,
				'db_inited' => true,
				'version' => $version,
				'priority' => $priority
			]];
			$this->Plugin->create($data);
		}

		// データを保存
		if ($this->Plugin->save()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * プラグインを初期化
	 *
	 * @param $_path
	 */
	public function initPlugin($_path, $dbDataPattern = '')
	{
		if ($dbDataPattern) {
			$_SESSION['dbDataPattern'] = $dbDataPattern;
		}
		ClassRegistry::flush();
		if (file_exists($_path)) {
			try {
				set_time_limit(0);
				include $_path;
			} catch (Exception $e) {
				$this->log($e->getMessage());
			}
		}
	}

	/**
	 * プラグインをアンインストールする
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function uninstallPlugin($name)
	{

		$Plugin = ClassRegistry::init('Plugin');
		$data = $Plugin->find('first', ['conditions' => ['Plugin.name' => $name], 'recursive' => -1]);
		$data['Plugin']['status'] = false;
		if ($Plugin->save($data)) {
			clearAllCache();
			return true;
		} else {
			return false;
		}

	}

	/**
	 * 初期データチェックする
	 *
	 * @param string $dbConfigKeyName
	 * @param array $dbConfig
	 * @param string $pattern
	 * @param string $theme
	 * @param string $plugin
	 * @return boolean
	 */
	public function checkDefaultDataPattern($pattern, $theme = 'core')
	{
		$path = BcUtil::getDefaultDataPath('core', $theme, $pattern);
		if (!$path) {
			return false;
		}
		$corePath = BcUtil::getDefaultDataPath('core', 'core', 'default');

		$Folder = new Folder($corePath);
		$files = $Folder->read(true, true);
		$coreTables = $files[1];
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);
		if (empty($files[1])) {
			return false;
		}
		// よく使う項目は、user_groups より生成するのでなくてもよい
		$excludes = ['favorites.csv'];
		$targetTables = $files[1];
		foreach($coreTables as $coreTable) {
			if (in_array($coreTable, $excludes)) {
				continue;
			}
			if (!in_array($coreTable, $targetTables)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * テーマに梱包されているプラグインをインストールする
	 *
	 * @param string $theme テーマ名
	 * @return bool
	 */
	public function installThemesPlugins($theme)
	{
		$plugins = BcUtil::getThemesPlugins($theme);
		$result = true;
		if ($plugins) {
			App::build(['Plugin' => array_merge([BASER_THEMES . $theme . DS . 'Plugin' . DS], App::path('Plugin'))]);
			foreach($plugins as $plugin) {
				if (!$this->installPlugin($plugin)) {
					$result = false;
				}
			}
		}
		return $result;
	}

}
