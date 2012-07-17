<?php
/* SVN FILE: $Id$ */
/**
 * インストーラーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			cake
 * @subpackage		cake.app.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * インストール条件
 * 
 *  @global string PHP_MINIMUM_VERSION
 *  @global integer PHP_MINIMUM_MEMORY_LIMIT in MB
 */
define("PHP_MINIMUM_VERSION","4.3.0");
define("PHP_MINIMUM_MEMORY_LIMIT", 16);
/**
 * インストーラーコントローラー
 */
class InstallationsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access	public
 */
	var $name = 'Installations';
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('Session', 'BcEmail', 'BcManager');
/**
 * レイアウトパス
 *
 * @var string
 * @access	public
 */
	var $layoutPath = 'admin';
/**
 * サブフォルダ
 *
 * @var string
 * @access	public
 */
	var $subDir = 'admin';
/**
 * ヘルパー
 *
 * @var array
 * @access	public
 */
	var $helpers = array(BC_HTML_HELPER, BC_FORM_HELPER, 'Javascript', BC_TIME_HELPER);
/**
 * モデル
 *
 * @var array
 * @access	public
 */
	var $uses = null;
/**
 * データベースエラーハンドラ
 *
 * @param int $errno
 * @param string	$errstr
 * @param string	$errfile
 * @param int $errline
 * @param string	$errcontext
 * @return void
 * @access public
 */
	function dbErrorHandler( $errno, $errstr, $errfile=null, $errline=null, $errcontext=null ) {

		if ($errno==2) {
			$this->Session->setFlash("データベースへの接続でエラーが発生しました。データベース設定を見直してください。<br />".$errstr);
			restore_error_handler();
		}

	}
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();
		
		/* インストール状態判別 */
		if(file_exists(CONFIGS.'database.php')) {
			$db = ConnectionManager::getInstance();
			if($db->config->baser['driver'] != '') {
				$installed = 'complete';
			}else {
				$installed = 'half';
			}
		}else {
			$installed = 'yet';
		}

		switch ($this->action) {
			case 'alert':
				break;
			case 'reset':
				if(Configure::read('debug') != -1) {
					$this->notFound();
				}
				break;
			default:
				if($installed == 'complete') {
					if($this->action != 'step5') {
						$this->notFound();
					}
				}else {
					if(Configure::read('debug') == 0) {
						$this->redirect(array('action'=>'alert'));
					}
				}
				break;
		}

		if (strpos($this->webroot, 'webroot') === false) {
			$this->webroot = DS;
		}

		$this->theme = null;

		$this->Security->validatePost = false;

	}
/**
 * Step 1: ウェルカムページ
 *
 * @return void
 * @access public
 */
	function index() {

		$this->pageTitle = 'baserCMSのインストール';

		// 一時ファイルを削除する（再インストール用）
		if(is_writable(TMP)) {
			$folder = new Folder(TMP);
			$files = $folder->read(true, true, true);
			if(isset($files[0])) {
				foreach($files[0] as $file) {
					$folder->delete($file);
				}
			}
			if(isset($files[1])) {
				foreach($files[1] as $file) {
					if(basename($file) != 'empty') {
						$folder->delete($file);
					}
				}
			}
		}

	}
/**
 * Step 2: 必須条件チェック
 *
 * @return void
 * @access public
 */
	function step2() {

		if($this->data && $this->data['clicked']=='next'){
			$this->redirect('step3');
		}

		// PHPバージョンチェック
		$phpVersionOk= version_compare ( preg_replace('/[a-z-]/','', phpversion()),PHP_MINIMUM_VERSION,'>=');
		// PHP memory limit チェック
		$phpCurrentMemoryLimit = intval(ini_get('memory_limit'));
		$phpMemoryOk = ((($phpCurrentMemoryLimit >= PHP_MINIMUM_MEMORY_LIMIT) || $phpCurrentMemoryLimit == -1) === TRUE);
		// セーフモード
		$safeModeOff = !ini_get('safe_mode');
		// configs 書き込み権限
		$configDirWritable = is_writable(CONFIGS);
		// core.phpの書き込み権限
		$coreFileWritable = is_writable(CONFIGS.'core.php');
		// DEMO用のページディレクトリの書き込み権限
		$themeDirWritable = is_writable(WWW_ROOT.'themed');
		// 一時フォルダの書き込み権限
		$tmpDirWritable = is_writable(TMP);
		// SQLiteディレクトリ書き込み権限
		$dbDirWritable = is_writable(APP.'db');

		/* ダミーのデータベース設定ファイルを保存 */
		$this->_writeDatabaseConfig();

		/* viewに変数をセット */
		$this->set('phpVersionOk', $phpVersionOk);
		$this->set('phpActualVersion', preg_replace('/[a-z-]/','', phpversion()));
		$this->set('phpMinimumVersion', PHP_MINIMUM_VERSION);
		$this->set('phpMinimumMemoryLimit', PHP_MINIMUM_MEMORY_LIMIT);
		$this->set('phpCurrentMemoryLimit', $phpCurrentMemoryLimit);
		$this->set('phpMemoryOk', $phpMemoryOk);
		$this->set('configDirWritable', $configDirWritable);
		$this->set('coreFileWritable',$coreFileWritable);
		$this->set('safeModeOff', $safeModeOff);
		$this->set('dbDirWritable',$dbDirWritable);
		$this->set('tmpDirWritable',$tmpDirWritable);
		$this->set('themeDirWritable',$themeDirWritable);
		$this->set('blRequirementsMet', ($tmpDirWritable && $configDirWritable && $coreFileWritable && $phpVersionOk && $themeDirWritable));
		$this->pageTitle = 'baserCMSのインストール [ステップ２]';

	}
/**
 * Step 3: データベースの接続設定
 * 
 * @return void
 * @access public
 */
	function step3() {

		if(!$this->data) {
			$this->data = $this->_getDefaultValuesStep3();
		} else {

			$this->_writeDbSettingToSession($this->data['Installation']);

			/* 戻るボタンクリック時 */
			if ($this->data['buttonclicked']=='back') {
				$this->redirect('step2');

			/* 接続テスト */
			} elseif ($this->data['buttonclicked']=='checkdb') {

				$this->set('blDBSettingsOK',$this->_testConnectDb($this->_readDbSettingFromSession()));

			/* 「次のステップへ」クリック時 */
			} elseif ($this->data['buttonclicked']=='createdb') {
				
				ini_set("max_execution_time",180);
				
				$nonDemoData = false;
				if(isset($this->data['Installation']['non_demo_data'])) {
					$nonDemoData = $this->data['Installation']['non_demo_data'];
				}
				$this->deleteAllTables();
				if($this->_constructionDb($nonDemoData)) {
					$this->Session->setFlash("データベースの構築に成功しました。");
					$this->redirect('step4');
				}else {
					$db =& ConnectionManager::getDataSource('baser');
					$this->Session->setFlash("データベースの構築中にエラーが発生しました。<br />".$db->error);
				}

			}

		}

		$this->pageTitle = 'baserCMSのインストール [ステップ３]';
		$this->set('dbsource', $this->_getDbSource());

	}
/**
 * Step 4: データベース生成／管理者ユーザー作成
 *
 * @return void
 * @access public
 */
	function step4() {

		if(!$this->data) {
			$this->data = $this->_getDefaultValuesStep4();
		} else {

			// ユーザー情報をセッションに保存
			$this->Session->write('Installation.admin_email', $this->data['Installation']['admin_email']);
			$this->Session->write('Installation.admin_username', $this->data['Installation']['admin_username']);
			$this->Session->write('Installation.admin_password', $this->data['Installation']['admin_password']);

			if($this->data['clicked'] == 'back') {
				$this->redirect('step3');

			} elseif($this->data['clicked'] == 'finish') {

				// DB接続
				$db =& $this->_connectDb($this->_readDbSettingFromSession());

				// サイト基本設定登録
				App::import('Model','SiteConfig');
				$siteConfig['SiteConfig']['email'] = $this->data['Installation']['admin_email'];
				$SiteConfigClass = new SiteConfig();
				$SiteConfigClass->saveKeyValue($siteConfig);

				// 管理ユーザー登録
				$salt = $this->_createKey(40);
				Configure::write('Security.salt',$salt);
				$this->Session->write('Installation.salt',$salt);
				App::import('Model','User');
				$user = array();
				$user['User']['name'] = $this->data['Installation']['admin_username'];
				$user['User']['real_name_1'] = $this->data['Installation']['admin_username'];
				$user['User']['email'] = $this->data['Installation']['admin_email'];
				$user['User']['user_group_id'] = 1;
				$user['User']['password_1'] = $this->data['Installation']['admin_password'];
				$user['User']['password_2'] = $this->data['Installation']['admin_confirmpassword'];
				$user['User']['password'] = $user['User']['password_1'];
				$User = new User();
				$User->create($user);
				if ($User->validates()) {
					$user['User']['password'] = Security::hash($this->data['Installation']['admin_password'],null,true);
					$User->save($user,false);
					$this->_sendCompleteMail($user['User']['email'], $user['User']['name'], $this->data['Installation']['admin_password']);
					$this->redirect('step5');
				} else {
					$message = '管理ユーザーを作成できませんでした。<br />'.$db->error;
					$this->Session->setFlash($message);
				}
			}
		}

		$this->pageTitle = 'baserCMSのインストール [ステップ４]';

	}
/**
 * インストール完了メールを送信する
 *
 * @param	string	$email
 * @param	string	$name
 * @param	string	$password
 * @return void
 * @access protected
 */
	function _sendCompleteMail($email, $name, $password) {

		$body = array('name'=>$name, 'password'=>$password, 'siteUrl' => siteUrl());
		$this->sendMail($email, 'baserCMSインストール完了', $body, array('template'=>'installed', 'from'=>$email));

	}
/**
 * Step 5: 設定ファイルの生成
 * データベース設定ファイル[database.php]
 * インストールファイル[install.php]
 * 
 * @return void
 * @access public
 */
	function step5() {

		$this->pageTitle = 'baserCMSのインストール完了！';
			
		if(!BC_INSTALLED) {
			$installationData = $this->Session->read('Installation');
			$installationData['lastStep'] = true;
			checkTmpFolders();
			Configure::write('Cache.disable', false);
			Cache::config('default', array('engine' => 'File'));
			// インストールファイルでセッションの保存方法を切り替える為、インストール情報をキャッシュに保存
			Cache::write('Installation', $installationData, 'default');
			// データベース設定を書き込む
			$this->_writeDatabaseConfig($this->_readDbSettingFromSession());
			// インストールファイルを生成する
			$this->_createInstallFile();
			$this->redirect('step5');
		} elseif(BC_INSTALLED) {
			$installationData = Cache::read('Installation', 'default');
			if(empty($installationData['lastStep'])) {
				return;
			}
		}

		// ブログの投稿日を更新
		$this->_updateEntryDate();

		// プラグインのステータスを更新
		$this->_updatePluginStatus();

		// ログイン
		$this->_login();

		// テーマを配置する
		$this->BcManager->deployTheme();
		$this->BcManager->deployTheme('skelton');

		// pagesファイルを生成する
		$this->_createPages();
		ClassRegistry::removeObject('View');
		
		$this->Session->delete('InstallLastStep');
		
	}
/**
 * インストールファイルを生成する
 *
 * @return boolean
 * @access	protected
 */
	function _createInstallFile() {

		$corefilename=CONFIGS.'install.php';
		$siteUrl = siteUrl();
		$installCoreData = array("<?php",	
			"Configure::write('Security.salt', '".$this->Session->read('Installation.salt')."');",
			"Configure::write('Cache.disable', false);",
			"Configure::write('Session.save', 'cake');",
			"Configure::write('BcEnv.siteUrl', '{$siteUrl}');",
			"Configure::write('BcEnv.sslUrl', '');",
			"Configure::write('BcApp.adminSsl', false);",
			"Configure::write('BcApp.mobile', true);",
			"Configure::write('BcApp.smartphone', true);",
			"Cache::config('default', array('engine' => 'File'));",
			"Configure::write('debug', 0);",
		"?>");
		if(file_put_contents($corefilename, implode("\n", $installCoreData))) {
			return chmod($corefilename,0666);
		}else {
			return false;
		}

	}
/**
 * プラグインのステータスを更新する
 *
 * @return boolean
 * @access	protected
 */
	function _updatePluginStatus() {

		$this->_connectDb($this->_readDbSettingFromSession());
		$version = $this->getBaserVersion();
		App::import('Model', 'Plugin');
		$Plugin = new Plugin();
		$datas = $Plugin->find('all');
		if($datas){
			$result = true;
			foreach($datas as $data) {
				$data['Plugin']['version'] = $version;
				$data['Plugin']['status'] = true;
				if(!$Plugin->save($data)) {
					$result = false;
				}
			}
			return $result;
		} else {
			return false;
		}

	}
/**
 * 登録日を更新する
 *
 * @return boolean
 * @access	protected
 */
	function _updateEntryDate() {

		$db =& $this->_connectDb($this->_readDbSettingFromSession());
		$db =& $this->_connectDb($this->_readDbSettingFromSession(),'plugin');
		App::import('Model', 'Blog.BlogPost');
		$BlogPost = new BlogPost();
		$blogPosts = $BlogPost->find('all');
		if($blogPosts) {
			$ret = true;
			foreach($blogPosts as $blogPost) {
				$blogPost['BlogPost']['posts_date'] = date('Y-m-d H:i:s');
				if(!$BlogPost->save($blogPost)) {
					$ret = false;
				}
			}
			return $ret;
		} else {
			return false;
		}

	}
/**
 * 管理画面にログインする
 *
 * @return void
 * @access	protected
 */
	function _login() {

		$extra = array();
		// ログインするとセッションが初期化されてしまうので一旦取得しておく
		$installationSetting = Cache::read('Installation', 'default');
		Cache::delete('Installation', 'default');
		Configure::write('Security.salt', $installationSetting['salt']);
		$extra['data']['User']['name'] = $installationSetting['admin_username'];
		$extra['data']['User']['password'] = $installationSetting['admin_password'];
		$this->requestAction(array('admin' => true, 'controller' => 'users', 'action' => 'login_exec'), $extra);
		$this->Session->write('Installation', $installationSetting);

	}
/**
 * テーマ用のページファイルを生成する
 *
 * @access	protected
 */
	function _createPages() {

		App::import('Model','Page');
		$Page = new Page(null, null, 'baser');
		$pages = $Page->find('all', array('recursive' => -1));
		if($pages) {
			foreach($pages as $page) {
				$Page->data = $page;
				$Page->afterSave(true);
			}
		}

	}
/**
 * データベースに接続する
 *
 * @param array $config
 * @return DboSource $db
 * @access public
 */
	function &_connectDb($config, $name='baser') {

		if($name == 'plugin') {
			$config['prefix'].=Configure::read('BcEnv.pluginDbPrefix');
		}
		
		$result =  ConnectionManager::create($name ,array(
				'driver' => $config['driver'],
				'persistent' => false,
				'host' => $config['host'],
				'port' => $config['port'],
				'login' => $config['login'],
				'password' => $config['password'],
				'database' => $config['database'],
				'schema' => $config['schema'],
				'prefix' =>  $config['prefix'],
				'encoding' => $config['encoding']));

		if($result) {
			return $result;
		} else {
			return ConnectionManager::getDataSource($name);
		}

	}
/**
 * データベースを構築する
 * 
 * @param type $nonDemoData
 * @return boolean
 * @access protected
 */
	function _constructionDb($nonDemoData = false) {

		$dbConfig = $this->_readDbSettingFromSession();
		if(!$this->BcManager->constructionTable(BASER_CONFIGS.'sql', 'baser', $dbConfig, $nonDemoData)) {
			return false;
		}
		$dbConfig['prefix'].=Configure::read('BcEnv.pluginDbPrefix');
		if(!$this->BcManager->constructionTable(BASER_PLUGINS.'blog'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}
		if(!$this->BcManager->constructionTable(BASER_PLUGINS.'feed'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}
		if(!$this->BcManager->constructionTable(BASER_PLUGINS.'mail'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}

		return true;

	}
/**
 * ステップ３用のフォーム初期値を取得する
 *
 * @return array
 * @access	protected
 */
	function _getDefaultValuesStep3() {

		$data = array();
		if( $this->Session->read('Installation.dbType') ){
			$_data = $this->_readDbSettingFromSession();
			$data['Installation']['dbType'] = $_data['driver'];
			$data['Installation']['dbHost'] = $_data['host'];
			$data['Installation']['dbPort'] = $_data['port'];
			$data['Installation']['dbPrefix'] = $_data['prefix'];
			$_data['database'] = basename($_data['database']);
			$_data['database'] = str_replace(array('.csv', '.db'), '', $_data['database']);
			$_data['database'] = basename($_data['database']);
			$data['Installation']['dbName'] = $_data['database'];
			$data['Installation']['dbUsername'] = $_data['login'];
			$data['Installation']['dbPassword'] = $_data['password'];
		} else {
			$data['Installation']['dbType'] = 'mysql';
			$data['Installation']['dbHost'] = 'localhost';
			$data['Installation']['dbPort'] = '3306';
			$data['Installation']['dbPrefix'] = 'bc_';
			$data['Installation']['dbName'] = 'basercms';
		}
		return $data;

	}
/**
 * ステップ４用のフォーム初期値を取得する
 *
 * @return array
 * @access	protected
 */
	function _getDefaultValuesStep4() {

		$data = array();
		if ( $this->Session->read('Installation.admin_username') ) {
			$data['Installation']['admin_username'] = $this->Session->read('Installation.admin_username');
		} else {
			$data['Installation']['admin_username'] = 'admin';
		}
		if ( $this->Session->read('Installation.admin_password') ) {
			$data['Installation']['admin_password'] = $this->Session->read('Installation.admin_password');
			$data['Installation']['admin_confirmpassword'] = $data['Installation']['admin_password'];
		} else {
			$data['Installation']['admin_password'] = '';
		}
		if ( $this->Session->read('Installation.admin_email') ) {
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
 * @access	protected
 */
	function _readDbSettingFromSession() {

		$data = array();
		$data['driver'] = $this->Session->read('Installation.dbType');
		$data['host'] = $this->Session->read('Installation.dbHost');
		$data['port'] = $this->Session->read('Installation.dbPort');
		$data['login'] = $this->Session->read('Installation.dbUsername');
		$data['password'] = $this->Session->read('Installation.dbPassword');
		$data['prefix'] = $this->Session->read('Installation.dbPrefix');
		$data['database'] = $this->_getRealDbName($data['driver'], $this->Session->read('Installation.dbName'));
		$data['schema'] = $this->Session->read('Installation.dbSchema');
		$data['encoding'] = $this->Session->read('Installation.dbEncoding');
		$data['persistent'] = false;
		return $data;

	}
/**
 * 実際の設定用のDB名を取得する
 *
 * @param string	$type
 * @param string	$name
 * @return string
 * @access	protected
 */
	function _getRealDbName($type, $name) {

		/* dbName */
		if(!empty($type) && !empty($name)) {
			$type = preg_replace('/^bc_/', '', $type);
			if($type == 'sqlite3') {
				return APP.'db'.DS.'sqlite'.DS.$name.'.db';
			}elseif($type == 'csv') {
				return APP.'db'.DS.'csv'.DS.$name;
			}
		}
		return $name;

	}
/**
 * DB設定をセッションに保存
 *
 * @param array $data
 * @return void
 * @access	protected
 */
	function _writeDbSettingToSession($data) {

		/* dbEncoding */
		$data['dbEncoding'] = 'utf8';

		/* dbSchema */
		if($data['dbType'] == 'postgres') {
			$data['dbSchema'] = 'public'; // TODO とりあえずpublic固定
		}else {
			$data['dbSchema'] = '';
		}
		if($data['dbType'] == 'csv') {
			$data['dbEncoding'] = 'sjis';
		}
		
		$data['dbType'] = 'bc_'.$data['dbType'];
		

		$this->Session->write('Installation.dbType', $data['dbType']);
		$this->Session->write('Installation.dbHost', $data['dbHost']);
		$this->Session->write('Installation.dbPort', $data['dbPort']);
		$this->Session->write('Installation.dbUsername', $data['dbUsername']);
		$this->Session->write('Installation.dbPassword', $data['dbPassword']);
		$this->Session->write('Installation.dbPrefix', $data['dbPrefix']);
		$this->Session->write('Installation.dbName', $data['dbName']);
		$this->Session->write('Installation.dbSchema',$data['dbSchema']);
		$this->Session->write('Installation.dbEncoding',$data['dbEncoding']);

	}
/**
 * データベース接続テスト
 *
 * @param array $config
 * @return boolean
 * @access protected
 */
	function _testConnectDb($config){

		set_error_handler(array($this, "dbErrorHandler"));

		/* データベース接続生成 */

		$db =& $this->_connectDb($config);

		if ($db->connected) {

			/* 一時的にテーブルを作成できるかテスト */
			$randomtablename='deleteme'.rand(100,100000);
			$result = $db->execute("CREATE TABLE $randomtablename (a varchar(10))");

			if ($result) {
				$db->execute("drop TABLE $randomtablename");
				$this->Session->setFlash('データベースへの接続に成功しました。');
				return true;
			} else {
				$this->Session->setFlash("データベースへの接続でエラーが発生しました。<br />".$db->error);
			}

		} else {
			if (!$this->Session->read('Message.flash.message')) {
				if($db->connection){
					$this->Session->setFlash("データベースへの接続でエラーが発生しました。データベース設定を見直してください。<br />サーバー上に指定されたデータベースが存在しない可能性が高いです。");
				} else {
					$this->Session->setFlash("データベースへの接続でエラーが発生しました。データベース設定を見直してください。");
				}
			}
		}

		return false;

	}
/**
 * データベース設定ファイル[database.php]を保存する
 *
 * @param	array	$options
 * @return boolean
 * @access private
 */
	function _writeDatabaseConfig($options = array()) {

		if(!is_writable(CONFIGS)) {
			return false;
		}

		extract($options);

		if(!isset($driver)) {
			$driver = '';
		}
		if(!isset($host)) {
			$host = 'localhost';
		}
		if(!isset($port)) {
			$port = '';
		}
		if(!isset($login)) {
			$login = 'dummy';
		}
		if(!isset($password)) {
			$password = 'dummy';
		}
		if(!isset($database)) {
			$database = 'dummy';
		}
		if(!isset($prefix)) {
			$prefix = '';
		}
		if(!isset($schema)) {
			$schema = '';
		}
		if(!isset($encoding)) {
			$encoding = 'utf8';
		}

		App::import('File');

		$dbfilename=CONFIGS.'database.php';
		$dbfilehandler = & new File($dbfilename);

		if ($dbfilehandler!==false) {

			if ($dbfilehandler->exists()) {
				$dbfilehandler->delete();
			}

			if($driver == 'mysql' || $driver == 'sqlite3' || $driver == 'postgres') {
				$driver = 'bc_'.$driver;
			}

			$dbfilehandler->create();
			$dbfilehandler->open('w',true);
			$dbfilehandler->write("<?php\n");
			$dbfilehandler->write("//\n");
			$dbfilehandler->write("// Database Configuration File created by baserCMS Installation\n");
			$dbfilehandler->write("//\n");
			$dbfilehandler->write("class DATABASE_CONFIG {\n");
			$dbfilehandler->write('var $baser = array('."\n");
			$dbfilehandler->write("\t'driver' => '".$driver."',\n");
			$dbfilehandler->write("\t'persistent' => false,\n");
			$dbfilehandler->write("\t'host' => '".$host."',\n");
			$dbfilehandler->write("\t'port' => '".$port."',\n");
			$dbfilehandler->write("\t'login' => '".$login."',\n");
			$dbfilehandler->write("\t'password' => '".$password."',\n");
			$dbfilehandler->write("\t'database' => '".$database."',\n");
			$dbfilehandler->write("\t'schema' => '".$schema."',\n");
			$dbfilehandler->write("\t'prefix' => '".$prefix."',\n");
			$dbfilehandler->write("\t'encoding' => '".$encoding."'\n");
			$dbfilehandler->write(");\n");

			$dbfilehandler->write('var $plugin = array('."\n");
			$dbfilehandler->write("\t'driver' => '".$driver."',\n");
			$dbfilehandler->write("\t'persistent' => false,\n");
			$dbfilehandler->write("\t'host' => '".$host."',\n");
			$dbfilehandler->write("\t'port' => '".$port."',\n");
			$dbfilehandler->write("\t'login' => '".$login."',\n");
			$dbfilehandler->write("\t'password' => '".$password."',\n");
			$dbfilehandler->write("\t'database' => '".$database."',\n");
			$dbfilehandler->write("\t'schema' => '".$schema."',\n");
			$dbfilehandler->write("\t'prefix' => '".$prefix.Configure::read('BcEnv.pluginDbPrefix')."',\n");
			$dbfilehandler->write("\t'encoding' => '".$encoding."'\n");
			$dbfilehandler->write(");\n");
			$dbfilehandler->write("}\n");
			$dbfilehandler->write("?>\n");

			$dbfilehandler->close();
			return true;

		} else {
			return false;
		}

	}
/**
 * 利用可能なデータソースを取得する
 *
 * @return array
 * @access	protected
 */
	function _getDbSource() {

		/* DBソース取得 */
		$dbsource = array();
		$folder = new Folder();

		/* MySQL利用可否 */
		if(function_exists('mysql_connect')) {
			$dbsource['mysql'] = 'MySQL';
		}

		/* PostgreSQL利用可否 */
		if(function_exists('pg_connect')) {
			$dbsource['postgres'] = 'PostgreSQL';
		}

		/* SQLite利用可否チェック */
		if(class_exists('PDO') && version_compare ( preg_replace('/[a-z-]/','', phpversion()),'5','>=')) {

			$pdoDrivers = PDO::getAvailableDrivers();
			if(in_array('sqlite',$pdoDrivers)) {
				$dbFolderPath = APP.'db'.DS.'sqlite';
				if($folder->create($dbFolderPath, 0777) && is_writable($dbFolderPath)){
					$dbsource['sqlite3'] = 'SQLite3';
				}
			}else {
				// TODO SQLite2 実装
				// AlTER TABLE できないので、実装には、テーブル構造の解析が必要になる。
				// 一度一時テーブルを作成し、データを移動させた上で、DROP。
				// 新しい状態のテーブルを作成し、一時テーブルよりデータを移行。
				// その後、一時テーブルを削除する必要がある。
				// 【参考】http://seclan.dll.jp/dtdiary/2007/dt20070228.htm
				// プラグインコンテンツのアカウント変更時、メールフォームのフィールド変更時の処理を実装する必要あり
				//$dbsource['sqlite'] = 'SQLite';
			}

		}

		/* CSV利用可否 */
		$dbFolderPath = APP.'db'.DS.'csv';
		if($folder->create($dbFolderPath, 0777) && is_writable($dbFolderPath)){
			$dbsource['csv'] = 'CSV';
		}

		return $dbsource;

	}
/**
 * セキュリティ用のキーを生成する
 *
 * @param	int $length
 * @return string キー
 * @access	protected
 */
	function _createKey($length) {

		$keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$randkey = "";
		for ($i=0; $i<$length; $i++)
			$randkey .= substr($keyset, rand(0,strlen($keyset)-1), 1);
		return $randkey;

	}
/**
 * インストール不能警告メッセージを表示
 *
 * @return void
 * @access public
 */
	function alert() {
		
		$this->pageTitle = 'baserCMSのインストールを開始できません';
		
	}
/**
 * baserCMSを初期化する
 * debug フラグが -1 の場合のみ実行可能
 *
 * @return	void
 * @access	public
 */
	function reset() {

		$this->pageTitle = 'baserCMSの初期化';
		$this->layoutPath = 'admin';
		$this->layout = 'default';
		$this->subDir = 'admin';

		if(!empty($this->data['Installation']['reset'])) {

			$messages = array();
			$file = new File(CONFIGS.'core.php');
			$data = $file->read();
			$pattern = '/Configure\:\:write[\s]*\([\s]*\'App\.baseUrl\'[\s]*,[\s]*\'\'[\s]*\);\n/is';
			if(preg_match($pattern, $data)) {
				$data = preg_replace($pattern, "Configure::write('App.baseUrl', env('SCRIPT_NAME'));\n", $data);
				if(!$file->write($data)){
					$messages[] = 'スマートURLの設定を正常に初期化できませんでした。';
				}
				$file->close();
			}
			if(!$this->writeSmartUrl(false)){
				$messages[] = 'スマートURLの設定を正常に初期化できませんでした。';
			}
			
			$this->deleteAllTables();
			if(file_exists(CONFIGS.'database.php')) {
				// データベースのデータを削除
				unlink(CONFIGS.'database.php');
			}
			if(file_exists(CONFIGS.'install.php')) {
				unlink(CONFIGS.'install.php');
			}
			
			$themeFolder = new Folder(WWW_ROOT.'themed');
			$themeFiles = $themeFolder->read(true,true,true);
			foreach($themeFiles[0] as $theme){
				$pagesFolder = new Folder($theme.DS.'pages');
				$pathes = $pagesFolder->read(true,true,true);
				foreach($pathes[0] as $path){
					if(basename($path) != 'admin') {
						$folder = new Folder();
						$folder->delete($path);
						$folder = null;
					}
				}
				foreach($pathes[1] as $path){
					if(basename($path) != 'empty') {
						unlink($path);
					}
				}
				$pagesFolder = null;
			}
			$themeFolder = null;

			if($messages) {
				$messages[] = '手動でサーバー上より上記ファイルを削除して初期化を完了させてください。';
			}

			$messages = am(array('baserCMSを初期化しました。',''),$messages);

			$message = implode('<br />', $messages);
			ClassRegistry::flush();
			clearAllCache();
			$this->Session->setFlash($message);
			// アクション名で指定した場合、環境によっては正常にリダイレクトできないのでスマートURLオフのフルパスで記述
			$this->redirect('reset');
			$this->redirect('/index.php/installations/reset');
			
		} elseif(!BC_INSTALLED) {
			$complete = true;
		}else {
			$complete = false;
		}

		$this->set('complete', $complete);

	}
/**
 * 全てのテーブルを削除する
 *
 * @return void
 * @access public 
 */
	function deleteAllTables() {
		
		$baserConfig = $this->_readDbSettingFromSession();
		if(!$baserConfig) {
			$baserConfig = getDbConfig('baser');
			$pluginConfig = getDbConfig('plugin');
		} else {
			$pluginConfig = $baserConfig;
			$pluginConfig['prefix'] .= Configure::read('BcEnv.pluginDbPrefix');
		}
		if($baserConfig) {
			$this->BcManager->deleteTables('baser', $baserConfig);
		}
		if($pluginConfig) {
			$this->BcManager->deleteTables('plugin', $pluginConfig);
		}
		
	}
	
}
?>