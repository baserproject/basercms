<?php
/* SVN FILE: $Id$ */
/**
 * インストーラーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
			$this->setMessage("データベースへの接続でエラーが発生しました。データベース設定を見直してください。<br />".$errstr, true);
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

		$checkResult = $this->BcManager->checkEnv();

		/* ダミーのデータベース設定ファイルを保存 */
		$this->BcManager->createDatabaseConfig();

		$this->set($checkResult);
		
		extract($checkResult);
		
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

		$dbsource = $this->Session->read('Installation.dbSource');
		if(!$dbsource) {
			$dbsource = $this->_getDbSource();
			$this->Session->write('Installation.dbSource', $dbsource);
		}
		
		if(!$this->data) {
			clearAllCache();
			checkTmpFolders();
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
				
				$dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
				if(isset($this->data['Installation']['dbDataPattern'])) {
					$dbDataPattern = $this->data['Installation']['dbDataPattern'];
				}
				$this->deleteAllTables();
				if($this->_constructionDb($dbDataPattern)) {
					$this->setMessage("データベースの構築に成功しました。");
					$this->redirect('step4');
				}else {
					$db =& ConnectionManager::getDataSource('baser');
					$this->setMessage("データベースの構築中にエラーが発生しました。<br />".$db->error, true);
				}

			}

		}

		$dbDataPatterns = $this->BcManager->getAllDefaultDataPatterns();
		$this->set('dbDataPatterns', $dbDataPatterns);
		$this->pageTitle = 'baserCMSのインストール [ステップ３]';
		$this->set('dbsource', $dbsource);

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
				$db =& $this->BcManager->connectDb($this->_readDbSettingFromSession());

				// サイト基本設定登録
				$this->BcManager->setAdminEmail($this->data['Installation']['admin_email']);

				// SecuritySalt設定
				$salt = $this->BcManager->setSecuritySalt();
				$this->Session->write('Installation.salt',$salt);
				
				// 管理ユーザー登録
				$user = array(
					'name'		=> $this->data['Installation']['admin_username'],
					'password_1'=> $this->data['Installation']['admin_password'],
					'password_2'=> $this->data['Installation']['admin_confirmpassword'],
					'email'		=> $this->data['Installation']['admin_email']
				);

				if ($this->BcManager->addDefaultUser($user)) {
					$this->_sendCompleteMail($user['email'], $user['name'], $user['password_1']);
					$this->redirect('step5');
				} else {
					$this->setMessage('管理ユーザーを作成できませんでした。<br />'.$db->error, true);
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
			Configure::write('Cache.disable', false);
			Cache::config('default', array('engine' => 'File'));
			// インストールファイルでセッションの保存方法を切り替える為、インストール情報をキャッシュに保存
			Cache::write('Installation', $installationData, 'default');
			// データベース設定を書き込む
			$this->BcManager->createDatabaseConfig($this->_readDbSettingFromSession());
			// インストールファイルを生成する
			$secritySalt = $this->Session->read('Installation.salt');
			$this->BcManager->createInstallFile($secritySalt);
			$this->redirect('step5');
		} elseif(BC_INSTALLED) {
			$installationData = Cache::read('Installation', 'default');
			if(empty($installationData['lastStep'])) {
				return;
			}
		}

		// データベースのデータを初期設定に更新
		$this->BcManager->executeDefaultUpdates($this->_readDbSettingFromSession());

		// ログイン
		$this->_login();

		// テーマを配置する
		$this->BcManager->deployTheme();
		
		// エディタテンプレート用の画像を配置
		$this->BcManager->deployEditorTemplateImage();

		// pagesファイルを生成する
		$this->BcManager->createPageTemplates();
		
		$this->Session->delete('InstallLastStep');
		
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
 * データベースを構築する
 * 
 * @param type $nonDemoData
 * @return boolean
 * @access protected
 */
	function _constructionDb($dbDataPattern = false) {

		$dbConfig = $this->_readDbSettingFromSession();
		if(!$this->BcManager->constructionDb($dbConfig, $dbDataPattern)) {
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

		$defaultTheme = Configure::read('BcApp.defaultTheme');
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
			$data['Installation']['dbDataPattern'] = $_data['dataPattern'];
		} else {
			$data['Installation']['dbType'] = 'mysql';
			$data['Installation']['dbHost'] = 'localhost';
			$data['Installation']['dbPort'] = '3306';
			$data['Installation']['dbPrefix'] = 'bc_';
			$data['Installation']['dbName'] = 'basercms';
			$data['Installation']['dbDataPattern'] = $defaultTheme . '.default';
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
		$data['database'] = $this->BcManager->getRealDbName($data['driver'], $this->Session->read('Installation.dbName'));
		$data['schema'] = $this->Session->read('Installation.dbSchema');
		$data['encoding'] = $this->Session->read('Installation.dbEncoding');
		$data['dataPattern'] = $this->Session->read('Installation.dbDataPattern');
		$data['persistent'] = false;
		return $data;

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
		$this->Session->write('Installation.dbDataPattern',$data['dbDataPattern']);

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

		$db =& $this->BcManager->connectDb($config);

		if ($db->connected) {

			/* 一時的にテーブルを作成できるかテスト */
			$randomtablename='deleteme'.rand(100,100000);
			$result = $db->execute("CREATE TABLE $randomtablename (a varchar(10))");

			if ($result) {
				$db->execute("drop TABLE $randomtablename");
				$this->setMessage('データベースへの接続に成功しました。');
				return true;
			} else {
				
				$this->setMessage("データベースへの接続でエラーが発生しました。<br />".$db->error, true);
			}

		} else {
			
			if (!$this->Session->read('Message.flash.message')) {
				if($db->connection){
					$this->setMessage("データベースへの接続でエラーが発生しました。データベース設定を見直してください。<br />サーバー上に指定されたデータベースが存在しない可能性が高いです。", true);
				} else {
					$this->setMessage("データベースへの接続でエラーが発生しました。データベース設定を見直してください。", true);
				}
			}
		}

		return false;

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

			$dbConfig = $this->_readDbSettingFromSession();
			if(!$dbConfig) {
				$dbConfig = getDbConfig('baser');
			}
			
			if(!$this->BcManager->reset($dbConfig)) {
				$this->setMessage('baserCMSを初期化しましたが、正常に処理が行われませんでした。詳細については、エラー・ログを確認してださい。', true);
			} else {
				$this->setMessage('baserCMSを初期化しました。');
			}
			
			// スマートURLオンの際、アクション名でリダイレクトを指定した場合、
			// 環境によっては正常にリダイレクトできないのでスマートURLオフのフルパスで記述
			if(Configure::read('App.baseUrl')){
				$this->redirect('reset');
			} else {
				$this->redirect('/index.php/installations/reset');
			}
			
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
		
		$dbConfig = $this->_readDbSettingFromSession();
		if(!$dbConfig) {
			$dbConfig = getDbConfig();
		}
		$this->BcManager->deleteAllTables($dbConfig);
		
	}
	
}
