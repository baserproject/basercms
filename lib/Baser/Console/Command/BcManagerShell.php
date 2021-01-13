<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Console.Command
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('AppShell', 'Console/Command');
App::uses('BcManagerComponent', 'Controller/Component');

/**
 * インストール用シェルスクリプト
 *
 * @package Baser.Console.Command
 * @property BcManagerComponent $BcManager
 */
class BcManagerShell extends AppShell
{

	/**
	 * get the option parser
	 *
	 * @return void
	 */
	public function getOptionParser()
	{
		$parser = parent::getOptionParser();
		$parser->addOption('host', [
			'help' => __d('baser', 'ホスト名'),
			'default' => 'localhost'
		])->addOption('database', [
			'help' => __d('baser', 'データベース名'),
			'default' => ''
		])->addOption('login', [
			'help' => __d('baser', 'データベースログインユーザー名'),
			'default' => ''
		])->addOption('password', [
			'help' => __d('baser', 'データベースログインパスワード'),
			'default' => ''
		])->addOption('prefix', [
			'help' => __d('baser', 'データベーステーブルプレフィックス'),
			'default' => 'mysite_'
		])->addOption('port', [
			'help' => __d('baser', 'データベースポート番号'),
			'default' => ''
		])->addOption('baseurl', [
			'help' => __d('baser', 'ベースとなるURL'),
			'default' => '/'
		])->addOption('data', [
			'help' => __d('baser', '初期データパターン'),
			'default' => 'core.m-single'
		]);

		return $parser;
	}

	/**
	 * startup
	 */
	public function startup()
	{
		parent::startup();
		$this->BcManager = new BcManagerComponent(new ComponentCollection());
	}

	/**
	 * インストール
	 *
	 * cake bc_manager install "サイト名" "データベースの種類" "管理者アカウント名" "管理者パスワード" "管理者Eメール" -host "DBホスト名" -database "DB名" -login "DBユーザー名" -password "DBパスワード" -prefix "DBプレフィックス" -port "DBポート" -baseurl "RewriteBaseに設定するURL"
	 */
	public function install()
	{
		if (BC_INSTALLED) {
			$this->err(__d('baser', '既にインストール済です。 cake bc_manager reset を実行してください。'));
			return;
		}
		if (Configure::read('debug') != -1) {
			$this->err(__d('baser', 'baserCMSのインストールを行うには、debug を -1 に設定する必要があります。'));
			return false;
		}
		if (!$this->_install()) {
			$this->err(__d('baser', 'baserCMSのインストールに失敗しました。ログファイルを確認してください。'));
		}

		$Folder = new Folder();
		$Folder->delete(TMP . 'cache');
		$Folder->delete(TMP . 'logs');
		$Folder->delete(TMP . 'schemas');
		$Folder->delete(TMP . 'sessions');
		$this->out(__d('baser', 'baserCMSのインストールが完了しました。'));
	}

	/**
	 * reset
	 *
	 * cake bc_manager reset
	 */
	public function reset()
	{
		if (Configure::read('debug') != -1) {
			$this->err(__d('baser', 'baserCMSの初期化を行うには、debug を -1 に設定する必要があります。'));
			return false;
		}
		if (!$this->_reset()) {
			$this->err(__d('baser', 'baserCMSのリセットに失敗しました。ログファイルを確認してください。'));
		}
		$this->out(__d('baser', 'baserCMSのリセットが完了しました。'));
	}

	/**
	 * 再インストール
	 *
	 * コマンドはインストールと同じ
	 */
	public function reinstall()
	{
		if (Configure::read('debug') != -1) {
			$this->err(__d('baser', 'baserCMSの初期化を行うには、debug を -1 に設定する必要があります。'));
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
			$this->err(__d('baser', 'baserCMSの再インストールに失敗しました。ログファイルを確認してください。'));
		}
	}

	/**
	 * 環境チェック
	 *
	 * cake bc_manager checkenv
	 */
	public function checkenv()
	{
		$checkResult = $this->BcManager->checkEnv();
		$this->out(__d('baser', '基本必須条件'));
		$this->hr();
		$this->out('* PHP mbstring (' . $checkResult['encoding'] . ')：' . (($checkResult['encodingOk'])? 'OK' : 'NG'));
		if (!$checkResult['encodingOk']) {
			$this->out('　' . __d('baser', 'mbstring.internal_encoding を UTF-8 に設定してください。'));
		}
		$this->out('* PHP Version (' . $checkResult['phpVersion'] . ')：' . (($checkResult['phpVersionOk'])? 'OK' : 'NG'));
		if (!$checkResult['phpVersionOk']) {
			$this->out('　' . __d('baser', '古いバージョンのPHPです。動作保証はありません。'));
		}
		$this->out('* PHP Memory Limit (' . $checkResult['phpMemory'] . 'MB)：' . (($checkResult['phpMemoryOk'])? 'OK' : 'NG'));
		if (!$checkResult['phpMemoryOk']) {
			$this->out('　' . sprintf(__d('baser', 'memoty_limit の設定値を %s MB 以上に変更してください。'), Configure::read('BcRequire.phpMemory')));
		}
		$this->out('* Writable /app/Config/ (' . (($checkResult['configDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['configDirWritable'])? 'OK' : 'NG'));
		if (!$checkResult['configDirWritable']) {
			$this->out('　' . __d('baser', '/app/Config/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
		}
		$this->out('* Writable /app/webroot/theme/ (' . (($checkResult['themeDirWritable'])? 'True' : 'False') . ')：' . (($checkResult['themeDirWritable'])? 'OK' : 'NG'));
		if (!$checkResult['themeDirWritable']) {
			$this->out('　' . __d('baser', '/app/webroot/theme/ に書き込み権限を与える事ができませんでした。手動で書き込み権限を与えてください。'));
		}
		$this->out('* PHP GD (' . (($checkResult['phpGd'])? 'True' : 'False') . ')');
		if (!$checkResult['phpGd']) {
			$this->out('　' . __d('baser', 'PHP の GD は、必須モジュールです。GDが利用可能な状態にしてください。'));
		}

		$this->hr();
		$this->out(__d('baser', 'オプション'));
		$this->hr();

		$this->out('* PHP Safe Mode (' . (!($checkResult['safeModeOff'])? 'On' : 'Off') . ')');
		if (!$checkResult['safeModeOff']) {
			$this->out('　' . __d('baser', 'Safe Mode が On の場合、動作保証はありません。'));
		}
		$this->out('* PHP GD (' . (($checkResult['phpGd'])? 'True' : 'False') . ')');
		if (!$checkResult['phpGd']) {
			$this->out('　' . __d('baser', 'PHP の GD は、推奨モジュールです。インストールされていない場合、画像処理ができません。'));
		}
		$this->out('* PHP PDO (' . (($checkResult['phpPdo'])? 'True' : 'False') . ')');
		if (!$checkResult['phpPdo']) {
			$this->out('　' . __d('baser', 'PHP の PDO は推奨モジュールです。インストールされていない場合、SQLite は利用できません。'));
		}
		$this->out('* Writable /app/db/ (' . (($checkResult['dbDirWritable'])? 'True' : 'False') . ')');
		if (!$checkResult['dbDirWritable']) {
			$this->out('　' . __d('baser', '/app/db/ に書き込み権限を与える事ができませんでした。'));
			$this->out('　' . __d('baser', 'SQLite や CSV など、ファイルベースのデータベースを利用するには、'));
			$this->out('　' . __d('baser', '手動で書き込み権限を与えてください。'));
		}
		if ($checkResult['apacheRewrite']) {
			$apacheRewrite = 'True';
		} elseif ($checkResult['apacheRewrite'] == -1) {
			$apacheRewrite = __d('baser', '不明');
		} else {
			$apacheRewrite = 'False';
		}
		$this->out('* Apache Rewrite (' . $apacheRewrite . ')');
		if ($checkResult['apacheRewrite'] > 0) {
			$this->out('　' . __d('baser', 'Apache の Rewrite モジュール がインストールされていない場合、スマートURLは利用できません。'));
		}
		$this->hr();
	}

	/**
	 * インストール
	 */
	protected function _install()
	{
		if (count($this->args) < 2) {
			$this->err(__d('baser', '引数を見なおしてください。'));
			return false;
		}

		$dbConfig = $this->_getDbParams();
		if (!$dbConfig) {
			$this->err(__d('baser', '引数を見なおしてください。'));
			return false;
		}

		$siteUrl = $this->args[0];
		if (!preg_match('/\/$/', $siteUrl)) {
			$siteUrl .= '/';
		}

		$adminUser = [
			'name' => $this->args[2],
			'password' => $this->args[3],
			'email' => $this->args[4],
		];

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

		return $this->BcManager->install($siteUrl, $dbConfig, $adminUser, $baseUrl, $dataPattern);
	}

	/**
	 * パラメーターからDBの設定を取得する
	 * @return mixed Array Or false
	 */
	protected function _getDbParams()
	{
		$dbConfig = [
			'datasource' => '',
			'host' => '',
			'database' => '',
			'login' => '',
			'password' => '',
			'prefix' => '',
			'port' => '',
			'persistent' => false,
			'schema' => '',
			'encoding' => 'utf8'
		];

		if (!empty($this->args[1])) {
			$dbConfig['datasource'] = $this->args[1];
			$datasources = ['mysql', 'postgres', 'sqlite', 'csv'];
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
	protected function _reset()
	{
		$dbConfig = getDbConfig();
		return $this->BcManager->reset($dbConfig);
	}

}
