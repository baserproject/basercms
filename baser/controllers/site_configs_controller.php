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
 * [ADMIN] サイト基本設定
 *
 * @return	void
 * @access 	public
 */
	function admin_form() {

		if(empty($this->data)) {

			$this->data = $this->_getSiteConfigData();

		}else {

			$this->SiteConfig->set($this->data);

			if(!$this->SiteConfig->validates()) {

				$this->Session->setFlash('入力エラーです。内容を修正してください。');

			}else {

				$mode = 0;
				$smartUrl = false;
				$siteUrl = $sslUrl = $adminSslOn = '';
				if(isset($this->data['SiteConfig']['mode'])) {
					$mode = $this->data['SiteConfig']['mode'];
				}
				if(isset($this->data['SiteConfig']['smart_url'])) {
					$smartUrl = $this->data['SiteConfig']['smart_url'];
				}
				if(isset($this->data['SiteConfig']['ssl_url'])) {
					$siteUrl = $this->data['SiteConfig']['site_url'];
					if(!preg_match('/\/$/', $siteUrl)) {
						$siteUrl .= '/';
					}
				}
				if(isset($this->data['SiteConfig']['ssl_url'])) {
					$sslUrl = $this->data['SiteConfig']['ssl_url'];
					if($sslUrl && !preg_match('/\/$/', $sslUrl)) {
						$sslUrl .= '/';
					}
				}
				if(isset($this->data['SiteConfig']['admin_ssl_on'])) {
					$adminSslOn = $this->data['SiteConfig']['admin_ssl_on'];
				}
				unset($this->data['SiteConfig']['id']);
				unset($this->data['SiteConfig']['mode']);
				unset($this->data['SiteConfig']['smart_url']);
				unset($this->data['SiteConfig']['site_url']);
				unset($this->data['SiteConfig']['ssl_url']);
				unset($this->data['SiteConfig']['admin_ssl_on']);

				// DBに保存
				if($this->SiteConfig->saveKeyValue($this->data)) {

					$this->Session->setFlash('システム設定を保存しました。');

					// 環境設定を保存
					$this->writeDebug($mode);
					$this->writeInstallSetting('Baser.siteUrl', "'".$siteUrl."'");
					$this->writeInstallSetting('Baser.sslUrl', "'".$sslUrl."'");
					$this->writeInstallSetting('Baser.adminSslOn', ($adminSslOn)? 'true' : 'false');
					if($this->readSmartUrl() != $smartUrl) {
						$this->writeSmartUrl($smartUrl);
					}

					// キャッシュをクリア
					if($this->siteConfigs['maintenance'] || ($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme'])){
						clearViewCache();
					}

					// ページテンプレートの生成
					if($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme']) {
						if(!$this->Page->createAllPageTemplate()){
							$this->Session->setFlash(
									'テーマ変更中にページテンプレートの生成に失敗しました。<br />'.
									'表示できないページはページ管理より更新処理を行ってください。'
							);
						}
					}

					// リダイレクト
					if($this->readSmartUrl() != $smartUrl) {
						if($smartUrl){
							$redirectUrl = $this->getRewriteBase('/admin/site_configs/form');
						}else{
							$redirectUrl = $this->getRewriteBase('/index.php/admin/site_configs/form');
						}
						header('Location: '.FULL_BASE_URL.$redirectUrl);
						exit();
					}else{
						$this->redirect(array('action'=>'form'));
					}

				}

			}

		}

		/* スマートURL関連 */
		$apachegetmodules = function_exists('apache_get_modules');
		if($apachegetmodules) {
			$rewriteInstalled = in_array('mod_rewrite',apache_get_modules());
		}else {
			$rewriteInstalled = -1;
		}
		$writableInstall = is_writable(CONFIGS.'install.php');

		$docRoot = docRoot();

		if(preg_match('/'.str_replace('/', '\/', $docRoot).'/', ROOT)) {
			// webroot ≠ DOCUMENT_ROOT
			$htaccess1 = ROOT.DS.'.htaccess';
		} else {
			// webtoot = DOCUMENT_ROOT
			$htaccess1 = $docRoot.DS.'.htaccess';
		}
		$htaccess2 = WWW_ROOT.'.htaccess';

		$writableHtaccess = is_writable($htaccess1);
		if($htaccess1 != $htaccess2) {
			$writableHtaccess2 = is_writable($htaccess2);
		} else{
			$writableHtaccess2 = true;
		}
		$baseUrl = str_replace('/index.php', '', baseUrl());

		if($writableInstall && $writableHtaccess && $writableHtaccess2 && $rewriteInstalled !== false){
			$smartUrlChangeable = true;
		} else {
			$smartUrlChangeable = false;
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
/**
 * サイト基本設定データを取得する
 *
 * @return	void
 * @access	protected
 */
	function _getSiteConfigData() {

		$data['SiteConfig'] = $this->siteConfigs;
		$data['SiteConfig']['mode'] = $this->readDebug();
		$data['SiteConfig']['smart_url'] = $this->readSmartUrl();
		$data['SiteConfig']['site_url'] = Configure::read('Baser.siteUrl');
		$data['SiteConfig']['ssl_url'] = Configure::read('Baser.sslUrl');
		$data['SiteConfig']['admin_ssl_on'] = Configure::read('Baser.adminSslOn');
		return $data;

	}

}
?>