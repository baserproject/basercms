<?php

/**
 * インストール用シェルスクリプト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Console.Command
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('AppShell', 'Console/Command');
App::uses('BcManagerComponent', 'Controller/Component');

/**
 * インストール用シェルスクリプト
 * 
 * @package Baser.Console.Command
 */
class BcManagerShell extends AppShell {

/**
 * get the option parser
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addOption('host', array(
			'help' => 'ホスト名',
			'default' => 'localhost'
		))->addOption('database', array(
			'help' => 'データベース名',
			'default' => ''
		))->addOption('login', array(
			'help' => 'データベースログインユーザー名',
			'default' => ''
		))->addOption('password', array(
			'help' => 'データベースログインパスワード',
			'default' => ''
		))->addOption('prefix', array(
			'help' => 'データベーステーブルプレフィックス',
			'default' => 'mysite_'
		))->addOption('port', array(
			'help' => 'データベースポート番号',
			'default' => ''
		))->addOption('smarturl', array(
			'help' => 'スマートURLの利用',
			'default' => 'true'
		))->addOption('baseurl', array(
			'help' => 'ベースとなるURL',
			'default' => '/'
		))->addOption('data', array(
			'help' => '初期データパターン',
			'default' => 'core.m-single'
		));

		return $parser;
	}

/**
 * startup 
 */
	public function startup() {
		parent::startup();
		$this->BcManager = new BcManagerComponent(new ComponentCollection());
	}

/**
 * インストール 
 * 
 * cake bc_manager install "サイト名" "データベースの種類" "管理者アカウント名" "管理者パスワード" "管理者Eメール" -host "DBホスト名" -database "DB名" -login "DBユーザー名" -password "DBパスワード" -prefix "DBプレフィックス" -port "DBポート" -smarturl "スマートURL（true / false）" -baseurl "RewriteBaseに設定するURL"
 */
	public function install() {
		if (BC_INSTALLED) {
			$this->err("既にインストール済です。 cake bc_manager reset を実行してください。");
			return;
		}
		if (Configure::read('debug') != -1) {
			$this->err('baserCMSのインストールを行うには、debug を -1 に設定する必要があります。');
			return false;
		}
		if (!$this->_install()) {
			$this->err("baserCMSのインストールに失敗しました。ログファイルを確認してください。");
		}

		$Folder = new Folder();
		$Folder->delete(TMP . 'cache');
		$Folder->delete(TMP . 'logs');
		$Folder->delete(TMP . 'schemas');
		$Folder->delete(TMP . 'sessions');
		$this->out("baserCMSのインストールが完了しました。");
	}

/**
 * reset 
 * 
 * cake bc_manager reset
 */
	public function reset() {
		if (Configure::read('debug') != -1) {
			$this->err('baserCMSの初期化を行うには、debug を -1 に設定する必要があります。');
			return false;
		}
		if (!$this->_reset()) {
			$this->err("baserCMSのリセットに失敗しました。ログファイルを確認してください。");
		}
		$this->out("baserCMSのリセットが完了しました。");
	}

/**
 * 再インストール
 * 
 * コマンドはインストールと同じ
 */
	public function reinstall() {
		if (Configure::read('debug') != -1) {
			$this->err('baserCMSの初期化を行うには、debug を -1 に設定する必要があります。');
			return false;
		}
		$result = true;
		if (!$this->_reset()) {
			$result = false;
		}
		clearAllCache();
		if (!$this->_install()) {
			$result = false;
		}
		if (!$result) {
			$this->err("baserCMSの再インストールに失敗しました。ログファイルを確認してください。");
		}
	}

/**
 * 環境チェック
 * 
 * cake bc_manager checkenv
 */
	public function checkenv() {
		$checkResult = $this->BcManager->checkEnv();
		$this->out('基本必須条件');
		$this->hr();
		$this->out('* PHP mbstring (' . $checkResult['encoding'] . ')：' . (($checkResult['encodingOk']) ? 'OK' : 'NG'));
		if (!$checkResult['encodingOk']) {
			$this->out('　mbstring.internal_encoding を UTF-8 に設定してください。');
		}
		$this->out('* PHP Version (' . $checkResult['phpVersion'] . ')：' . (($checkResult['phpVersionOk']) ? 'OK' : 'NG'));
		if (!$checkResult['phpVersionOk']) {
			$this->out('　古いバージョンのPHPです。動作保証はありません。');
		}
		$this->out('* PHP Memory Limit (' . $checkResult['phpMemory'] . 'MB)：' . (($checkResult['phpMemoryOk']) ? 'OK' : 'NG'));
		if (!$checkResult['phpMemoryOk']) {
			$this->out('　memoty_limit の設定値を ' . Configure::read('BcRequire.phpMemory') . 'MB 以上に変更してください。');
		}
		$this->out('* Writable /app/Config/ (' . (($checkResult['configDirWritable']) ? 'True' : 'False') . ')：' . (($checkResult['configDirWritable']) ? 'OK' : 'NG'));
		if (!$checkResult['configDirWritable']) {
			$this->out('　/app/Config/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。');
		}
		$this->out('* Writable /app/Config/config.php (' . (($checkResult['coreFileWritable']) ? 'True' : 'False') . ')：' . (($checkResult['coreFileWritable']) ? 'OK' : 'NG'));
		if (!$checkResult['coreFileWritable']) {
			$this->out('　/app/Config/core.php に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。');
		}
		$this->out('* Writable /app/webroot/theme/ (' . (($checkResult['themeDirWritable']) ? 'True' : 'False') . ')：' . (($checkResult['themeDirWritable']) ? 'OK' : 'NG'));
		if (!$checkResult['themeDirWritable']) {
			$this->out('　/app/webroot/theme/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。');
		}
		$this->out('* PHP GD (' . (($checkResult['phpGd']) ? 'True' : 'False') . ')');
		if (!$checkResult['phpGd']) {
			$this->out('　PHP の GD は、必須モジュールです。GDが利用可能な状態にしてください。');
		}

		$this->hr();
		$this->out('オプション');
		$this->hr();

		$this->out('* PHP Safe Mode (' . (!($checkResult['safeModeOff']) ? 'On' : 'Off') . ')');
		if (!$checkResult['safeModeOff']) {
			$this->out('　Safe Mode が On の場合、動作保証はありません。');
		}
		$this->out('* PHP GD (' . (($checkResult['phpGd']) ? 'True' : 'False') . ')');
		if (!$checkResult['phpGd']) {
			$this->out('　PHP の GD は、推奨モジュールです。インストールされていない場合、画像処理ができません。');
		}
		$this->out('* PHP PDO (' . (($checkResult['phpPdo']) ? 'True' : 'False') . ')');
		if (!$checkResult['phpPdo']) {
			$this->out('　PHP の PDO は推奨モジュールです。インストールされていない場合、SQLite は利用できません。');
		}
		$this->out('* Writable /app/db/ (' . (($checkResult['dbDirWritable']) ? 'True' : 'False') . ')');
		if (!$checkResult['dbDirWritable']) {
			$this->out('　/app/db/ に書き込み権限を与える事ができませんでした。');
			$this->out('　SQLite や CSV など、ファイルベースのデータベースを利用するには、');
			$this->out('　手動で書き込み権限を与えてください。');
		}
		if ($checkResult['apacheRewrite']) {
			$apacheRewrite = 'True';
		} elseif ($checkResult['apacheRewrite'] == -1) {
			$apacheRewrite = '不明';
		} else {
			$apacheRewrite = 'False';
		}
		$this->out('* Apache Rewrite (' . $apacheRewrite . ')');
		if ($checkResult['apacheRewrite'] > 0) {
			$this->out('　Apache の Rewrite モジュール がインストールされていない場合、スマートURLは利用できません。');
		}
		$this->hr();
	}

/**
 * デモ用のCSVデータを初期化する
 */
	public function initdemo() {
		$dbConfig = getDbConfig();

		// データベース初期化
		if (!$this->BcManager->initDb($dbConfig)) {
			$message = "データベースの初期化に失敗しました";
			$this->log($message);
			$this->err($message);
			return;
		}

		// キャッシュ削除
		clearAllCache();

		// ユーザー作成
		if (!$this->_initDemoUsers()) {
			$message = "ユーザー「operator」の作成に失敗しました";
			$this->log($message);
			$this->err($message);
			return;
		}

		// サイト設定
		if (!$this->_initDemoSiteConfigs()) {
			$message = "システム設定の更新に失敗しました";
			$this->log($message);
			$this->err($message);
			return;
		}

		// DBデータの初期更新
		if (!$this->BcManager->executeDefaultUpdates($dbConfig)) {
			$message = "DBデータの初期更新に失敗しました。";
			$this->log($message);
			$this->err($message);
			return;
		}

		// テーマの配置
		if (!$this->BcManager->deployTheme()) {
			$message = "テーマの配置に失敗しました。";
			$this->log($message);
			$this->err($message);
			return;
		}

		// テーマに管理画面のアセットへのシンボリックリンクを作成する
		$this->BcManager->deleteDeployedAdminAssets();
		if (!$this->BcManager->deployAdminAssets()) {
			$message = "管理システムのアセットファイルの配置に失敗しました。";
			$this->log($message);
			$this->err($message);
		}

		// ページ初期化
		if (!$this->BcManager->createPageTemplates()) {
			$message = "ページテンプレートの更新に失敗しました";
			$this->log($message);
			$this->err($message);
			return;
		}

		clearAllCache();

		$this->out("デモデータを初期化しました。");
	}

/**
 * サイト設定の初期化
 * 
 * @return boolean
 */
	protected function _initDemoSiteConfigs() {
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$siteConfig = $SiteConfig->findExpanded();
		$siteConfig['address'] = '福岡県福岡市博多区博多駅前';
		$siteConfig['googlemaps_key'] = 'ABQIAAAAQMyp8zF7wiAa55GiH41tChRi112SkUmf5PlwRnh_fS51Rtf0jhTHomwxjCmm-iGR9GwA8zG7_kn6dg';
		$siteConfig['demo_on'] = true;
		return $SiteConfig->saveKeyValue($siteConfig);
	}

/**
 * 初期ユーザーの作成
 * 
 * @return boolean 
 */
	protected function _initDemoUsers() {
		$User = ClassRegistry::init('User');

		$ret = true;
		$user['User']['name'] = 'admin';
		$user['User']['password'] = Security::hash('demodemo', null, true);
		$user['User']['password_1'] = 'demodemo';
		$user['User']['password_2'] = 'demodemo';
		$user['User']['real_name_1'] = 'admin';
		$user['User']['user_group_id'] = 1;
		$User->create($user);
		if (!$User->save()) {
			$ret = false;
		}

		$user['User']['name'] = 'operator';
		$user['User']['password'] = Security::hash('demodemo', null, true);
		$user['User']['password_1'] = 'demodemo';
		$user['User']['password_2'] = 'demodemo';
		$user['User']['real_name_1'] = 'member';
		$user['User']['user_group_id'] = 2;
		$User->create($user);
		if (!$User->save()) {
			$ret = false;
		}

		return $ret;
	}

/**
 * インストール 
 */
	protected function _install() {
		if (count($this->args) < 2) {
			$this->err("引数を見なおしてください。");
			return false;
		}

		$dbConfig = $this->_getDbParams();
		if (!$dbConfig) {
			$this->err("引数を見なおしてください。");
			return false;
		}

		$siteUrl = $this->args[0];
		if (!preg_match('/\/$/', $siteUrl)) {
			$siteUrl .= '/';
		}

		$adminUser = array(
			'name' => $this->args[2],
			'password' => $this->args[3],
			'email' => $this->args[4],
		);

		if (isset($this->params['smarturl'])) {
			$smartUrl = (boolean)$this->params['smarturl'];
		} else {
			$smartUrl = false;
		}

		if (isset($this->params['baseurl'])) {
			$baseUrl = $this->params['baseurl'];
		} else {
			$baseUrl = '';
		}

		if (isset($this->params['data'])) {
			$dataPattern = $this->params['data'];
		} else {
			$dataPattern = 'core.demo';
		}

		return $this->BcManager->install($siteUrl, $dbConfig, $adminUser, $smartUrl, $baseUrl, $dataPattern);
	}

/**
 * パラメーターからDBの設定を取得する
 * @return mixed Array Or false
 */
	protected function _getDbParams() {
		$dbConfig = array(
			'datasource'	=> '',
			'host'			=> '',
			'database'		=> '',
			'login'			=> '',
			'password'		=> '',
			'prefix'		=> '',
			'port'			=> '',
			'persistent'	=> false,
			'schema'		=> '',
			'encoding'		=> 'utf8'
		);

		if (!empty($this->args[1])) {
			$dbConfig['datasource'] = $this->args[1];
			$datasources = array('mysql', 'postgres', 'sqlite', 'csv');
			if (!in_array($dbConfig['datasource'], $datasources)) {
				return false;
			}
		} else {
			return false;
		}

		if (!empty($this->params['login'])) {
			$dbConfig['login'] = $this->params['login'];
		} else {
			if ($dbConfig['datasource'] == 'mysql' || $dbConfig['datasource'] == 'postgres') {
				return false;
			}
		}
		if (!empty($this->params['password'])) {
			$dbConfig['password'] = $this->params['password'];
		} else {
			if ($dbConfig['datasource'] == 'mysql' || $dbConfig['datasource'] == 'postgres') {
				return false;
			}
		}
		if (!empty($this->params['host'])) {
			$dbConfig['host'] = $this->params['host'];
		} else {
			if ($dbConfig['datasource'] == 'mysql' || $dbConfig['datasource'] == 'postgres') {
				$dbConfig['host'] = 'localhost';
			}
		}
		if (!empty($this->params['prefix'])) {
			$dbConfig['prefix'] = $this->params['prefix'];
		} else {
			if ($dbConfig['datasource'] == 'mysql' || $dbConfig['datasource'] == 'postgres') {
				$dbConfig['prefix'] = 'mysite_';
			}
		}
		if (!empty($this->params['port'])) {
			$dbConfig['port'] = $this->params['port'];
		} else {
			if ($dbConfig['datasource'] == 'mysql') {
				$dbConfig['port'] = '3306';
			} elseif ($dbConfig['datasource'] == 'postgres') {
				$dbConfig['port'] = '5432';
			}
		}
		if (!empty($this->params['database'])) {
			$dbConfig['database'] = $this->params['database'];
		} else {
			$dbConfig['database'] = 'basercms';
		}
		$dbConfig['database'] = $this->BcManager->getRealDbName($dbConfig['datasource'], $dbConfig['database']);

		if ($dbConfig['datasource'] == 'postgres') {
			$dbConfig['schema'] = 'public';
		}

		return $dbConfig;
	}

/**
 * reset 
 */
	protected function _reset() {
		$dbConfig = getDbConfig();
		return $this->BcManager->reset($dbConfig);
	}

}
