<?php
/* SVN FILE: $Id$ */
/**
 * サイト設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * サイト設定コントローラー
 *
 * @package			baser.controllers
 */
class SiteConfigsController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'SiteConfigs';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('SiteConfig','GlobalMenu','Page');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ヘルパー
 * @var array
 */
	var $helpers = array('FormEx');
/**
 * ぱんくずナビ
 *
 * @var		array
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form');
/**
 * Folder Object
 *
 * @var Folder
 */
	var $Folder;
/**
 * beforeFilter
 * @return	void
 * @access	public
 */
	function beforeFilter() {

		parent::beforeFilter();

		// init Folder
		$this->Folder =& new Folder();
		$this->Folder->mode = 0777;

	}
/**
 * [ADMIN] サイト基本設定
 *
 * @return	void
 * @access 	public
 */
	function admin_form() {

		if(empty($this->data)) {
			$this->data = $this->SiteConfig->read(null, 1);
			$this->data['SiteConfig'] = $this->siteConfigs;
			$this->data['SiteConfig']['mode'] = $this->readDebug();
			$this->data['SiteConfig']['smart_url'] = $this->readSmartUrl();
			$this->data['SiteConfig']['site_url'] = Configure::read('Baser.siteUrl');
			$this->data['SiteConfig']['ssl_url'] = Configure::read('Baser.sslUrl');
			$this->data['SiteConfig']['admin_ssl_on'] = Configure::read('Baser.adminSslOn');
		}else {
			// テーブル構造が特殊なので強引にバリデーションを行う
			$this->SiteConfig->data = $this->data;

			if($this->data['SiteConfig']['admin_ssl_on'] && !$this->data['SiteConfig']['ssl_url']) {
				$this->SiteConfig->invalidate('ssl_url', '管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。');
			}

			if(!$this->SiteConfig->validates()) {
				
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
				
			}else {
				
				// KeyValueへ変換処理
				unset($this->data['SiteConfig']['id']);
				if(isset($this->data['SiteConfig']['mode'])) {
					$mode = $this->data['SiteConfig']['mode'];
					unset($this->data['SiteConfig']['mode']);
				} else {
					$mode = 0;
				}

				if(isset($this->data['SiteConfig']['smart_url'])) {
					$smartUrl = $this->data['SiteConfig']['smart_url'];
					unset($this->data['SiteConfig']['smart_url']);
				} else {
					$smartUrl = false;
				}

				$siteUrl = $this->data['SiteConfig']['site_url'];
				if(!preg_match('/\/$/', $siteUrl)) {
					$siteUrl .= '/';
				}
				unset($this->data['SiteConfig']['site_url']);

				if(isset($this->data['SiteConfig']['ssl_url'])) {
					$sslUrl = $this->data['SiteConfig']['ssl_url'];
					if(!preg_match('/\/$/', $sslUrl)) {
						$sslUrl .= '/';
					}
					unset($this->data['SiteConfig']['ssl_url']);
				} else {
					$sslUrl = '';
				}

				if(isset($this->data['SiteConfig']['admin_ssl_on'])) {
					$adminSslOn = $this->data['SiteConfig']['admin_ssl_on'];
					unset($this->data['SiteConfig']['admin_ssl_on']);
				} else {
					$adminSslOn = '';
				}

				$this->SiteConfig->saveKeyValue($this->data);
				$this->writeDebug($mode);
				$this->writeInstallSetting('Baser.siteUrl', "'".$siteUrl."'");
				$this->writeInstallSetting('Baser.sslUrl', "'".$sslUrl."'");
				$this->writeInstallSetting('Baser.adminSslOn', ($adminSslOn)? 'true' : 'false');
				if($this->readSmartUrl() != $smartUrl) {
					$this->writeSmartUrl($smartUrl);
				}
				if($this->siteConfigs['maintenance'] || ($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme'])){
					clearViewCache();
				}
				if($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme']) {
					if(!$this->Page->createAllPageTemplate()){
						$this->Session->setFlash('テーマ変更中にページテンプレートの生成に失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。');
						$this->redirect(array('action'=>'form'));
					}
				}
				$this->Session->setFlash('システム設定を保存しました。');

				if($this->readSmartUrl() != $smartUrl) {
					if($smartUrl){
						$redirectUrl = $this->getRewriteBase('/admin/site_configs/form');
					}else{
						$redirectUrl = $this->getRewriteBase('/index.php/admin/site_configs/form');
					}
					if($_SERVER['SERVER_PORT']=='443') {
						$protocol = 'https';
					} else {
						$protocol = 'http';
					}
					header('Location: '.$protocol.'://'.$_SERVER['HTTP_HOST'].$redirectUrl);
					exit();
				}else{
					$this->redirect(array('action'=>'form'));
				}

			}
		}

		/* スマートURL関連 */
		// mod_rewrite モジュールインストール
		$apachegetmodules = function_exists('apache_get_modules');
		if($apachegetmodules) {
			$rewriteInstalled = in_array('mod_rewrite',apache_get_modules());
		}else {
			$rewriteInstalled = -1;
		}
		$writableInstall = is_writable(CONFIGS.'install.php');
		$writableHtaccess = is_writable(ROOT.DS.'.htaccess');
		if(ROOT.DS.'.htaccess' != WWW_ROOT.'.htaccess') {
			$writableHtaccess2 = is_writable(WWW_ROOT.'.htaccess');
		} else{
			$writableHtaccess2 = true;
		}
		$baseUrl = str_replace('/index.php', '', baseUrl());
		
		if($writableInstall && $writableHtaccess && $writableHtaccess2 && $rewriteInstalled !== false){
			$smartUrlChangeable = true;
		} else {
			$smartUrlChangeable = false;
		}
		// バックアップ機能を実装しているデータベースの場合のみバックアップへのリンクを表示
		$enableBackupDb = array('sqlite','sqlite3','mysql','csv','postgres');
		$dbConfigs = new DATABASE_CONFIG();
		$dbConfig = $dbConfigs->{'baser'};
		$driver = str_replace('_ex','',$dbConfig['driver']);
		if(in_array($driver,$enableBackupDb)) {
			$this->set('backupEnabled',true);
		}

		$this->set('themes',$this->SiteConfig->getThemes());
		$this->set('rewriteInstalled', $rewriteInstalled);
		$this->set('writableInstall', $writableInstall);
		$this->set('writableHtaccess', $writableHtaccess);
		$this->set('writableHtaccess2', $writableHtaccess2);
		$this->set('baseUrl', $baseUrl);
		$this->set('smartUrlChangeable', $smartUrlChangeable);
		$this->subMenuElements = array('site_configs');
		$this->pageTitle = 'サイト基本設定';

	}
/**
 * キャッシュファイルを全て削除する
 * @return	void
 * @access	public
 */
	function admin_del_cache() {
		clearAllCache();
		$this->Session->setFlash('サーバーキャッシュを削除しました。');
		$this->redirect(array('action'=>'form'));
	}
/**
 * [ADMIN] PHPINFOを表示する
 * @return void
 * @access public
 */
	function admin_info() {

		if(!empty($this->siteConfigs['demo_on'])) {
			$this->notFound();
		}
		$this->pageTitle = '環境情報';
		$drivers = array('csv'=>'CSV','sqlite3'=>'SQLite3','mysql'=>'MySQL','postgres'=>'PostgreSQL');
		$smartUrl = 'ON';
		$db =& ConnectionManager::getDataSource('baser');
		if(Configure::read('App.baseUrl')){
			$smartUrl = 'OFF';
		}
		$driver = str_replace('_ex','',$db->config['driver']);
		$this->set('driver',$drivers[$driver]);
		$this->set('smartUrl',$smartUrl);
		$this->set('baserVersion',$this->siteConfigs['version']);
		$this->set('cakeVersion',$this->getCakeVersion());
		$this->subMenuElements = array('site_configs');

	}

}
?>