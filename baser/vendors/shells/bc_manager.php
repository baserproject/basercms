<?php
/* SVN FILE: $Id$ */
/**
 * インストール用シェルスクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.vendors.shells
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Vendor', 'BcApp');
App::import('Component','BcManager');
/**
 * インストール用シェルスクリプト
 * 
 * @package baser.vendors.shells
 */
class BcManagerShell extends BcAppShell {
/**
 * startup 
 */
	function startup() {
		
		parent::startup();
		$this->BcManager = new BcManagerComponent($this);
		
	}
/**
 * インストール 
 * 
 * cake bc_manager install "サイト名" "データベースの種類" "管理者アカウント名" "管理者パスワード" "管理者Eメール" -host "DBホスト名" -database "DB名" -login "DBユーザー名" -password "DBパスワード" -prefix "DBプレフィックス" -port "DBポート" -smarturl "スマートURL（true / false）" -baseurl "RewriteBaseに設定するURL"
 */
	function install() {
		if(!$this->_install()) {
			$this->err("baserCMSのインストールに失敗しました。ログファイルを確認してください。");
		}
	}
/**
 * インストール 
 */
	function _install() {

		if(count($this->args) < 2) {
			$this->err("引数を見なおしてください。");
			return false;
		}
		
		$dbConfig = $this->_getDbParams();
		if(!$dbConfig) {
			$this->err("引数を見なおしてください。");
			return false;
		}
		
		$siteUrl = $this->args[0];
		if(!preg_match('/\/$/', $siteUrl)) {
			$siteUrl .= '/';
		}

		$adminUser = array(
			'name'	=> $this->args[2],
			'password'	=> $this->args[3],
			'email'	=> $this->args[4],
		);
		
		if(isset($this->params['smarturl'])) {
			$smartUrl = (boolean) $this->params['smarturl'];
		} else {
			$smartUrl = false;
		}

		if(isset($this->params['baseurl'])) {
			$baseUrl = $this->params['baseurl'];
		} else {
			$baseUrl = '';
		}
		
		return $this->BcManager->install($siteUrl, $dbConfig, $adminUser, $smartUrl, $baseUrl);
		
	}
/**
 * パラメーターからDBの設定を取得する
 * @return mixed Array Or false
 */
	function _getDbParams() {
		
		$dbConfig = array(
			'driver'	=> '',
			'host'		=> '',
			'database'	=> '',
			'login'		=> '',
			'password'	=> '',
			'prefix'	=> '',
			'port'		=> '',
			'persistent'=> false,
			'schema'	=> '',
			'encoding'	=> 'utf8'
		);
		
		if(!empty($this->args[1])) {
			$dbConfig['driver'] = $this->args[1];
			$drivers = array('mysql', 'postgres', 'sqlite3', 'csv');
			if(in_array($dbConfig['driver'], $drivers) && !preg_match('/^bc_/', $dbConfig['driver'])) {
				$dbConfig['driver'] = 'bc_'.$dbConfig['driver'];
			}
		} else {
			return false;
		}
		
		if(!empty($this->params['login'])) {
			$dbConfig['login'] = $this->params['login'];
		} else {
			if($dbConfig['driver'] == 'bc_mysql' || $dbConfig['driver'] == 'bc_postgres') {
				return false;
			}
		}
		if(!empty($this->params['password'])) {
			$dbConfig['password'] = $this->params['password'];
		} else {
			if($dbConfig['driver'] == 'bc_mysql' || $dbConfig['driver'] == 'bc_postgres') {
				return false;
			}
		}
		if(!empty($this->params['host'])) {
			$dbConfig['host'] = $this->params['host'];
		} else {
			if($dbConfig['driver'] == 'bc_mysql' || $dbConfig['driver'] == 'bc_postgres') {
				$dbConfig['host'] = 'localhost';
			}
		}
		if(!empty($this->params['prefix'])) {
			$dbConfig['prefix'] = $this->params['prefix'];
		} else {
			if($dbConfig['driver'] == 'bc_mysql' || $dbConfig['driver'] == 'bc_postgres') {
				$dbConfig['prefix'] = 'bc_';
			}
		}
		if(!empty($this->params['port'])) {
			$dbConfig['port'] = $this->params['port'];
		} else {
			if($dbConfig['driver'] == 'bc_mysql') {
				$dbConfig['port'] = '3306';
			} elseif($dbConfig['driver'] == 'bc_postgres') {
				$dbConfig['port'] = '5432';
			}
		}
		if(!empty($this->params['database'])) {
			$dbConfig['database'] = $this->params['database'];
		} else {
			$dbConfig['database'] = 'basercms';
		}
		$dbConfig['database'] = $this->BcManager->getRealDbName($dbConfig['driver'], $dbConfig['database']);
		
		if($dbConfig['driver'] == 'bc_postgres') {
			$dbConfig['schema'] = 'public';
		}
		
		return $dbConfig;
		
	}
/**
 * reset 
 * 
 * cake bc_manager reset
 */
	function reset() {
		if(!$this->_reset()) {
			$this->err("baserCMSのリセットに失敗しました。ログファイルを確認してください。");
		}
	}
/**
 * reset 
 */
	function _reset() {
		
		$dbConfig = getDbConfig();
		return $this->BcManager->reset($dbConfig);
		
	}
/**
 * 再インストール
 * 
 * コマンドはインストールと同じ
 */
	function reinstall() {
		
		$result = true;
		if(!$this->_reset()) {
			$result = false;
		}
		if(!$this->_install()) {
			$result = false;
		}
		if(!$result) {
			$this->err("baserCMSの再インストールに失敗しました。ログファイルを確認してください。");
		}
		
	}
	
}