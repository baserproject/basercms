<?php
/* SVN FILE: $Id$ */
/**
 * サイト設定コントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
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
 * サイト設定コントローラー
 *
 * @package baser.controllers
 */
class SiteConfigsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'SiteConfigs';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('SiteConfig','GlobalMenu','Page');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure', 'BcManager');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ヘルパー
 * @var array
 * @access public
 */
	var $helpers = array(BC_FORM_HELPER, 'BcPage');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')));
/**
 * [ADMIN] サイト基本設定
 *
 * @return void
 * @access public
 */
	function admin_form() {

		if(empty($this->data)) {

			$this->data = $this->_getSiteConfigData();

		}else {

			$this->SiteConfig->set($this->data);

			if(!$this->SiteConfig->validates()) {

				$this->setMessage('入力エラーです。内容を修正してください。', true);

			}else {

				$mode = 0;
				$smartUrl = false;
				$siteUrl = $sslUrl = '';
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
				
				$adminSsl = $this->data['SiteConfig']['admin_ssl'];
				$mobile = $this->data['SiteConfig']['mobile'];
				$smartphone = $this->data['SiteConfig']['smartphone'];

				unset($this->data['SiteConfig']['id']);
				unset($this->data['SiteConfig']['mode']);
				unset($this->data['SiteConfig']['smart_url']);
				unset($this->data['SiteConfig']['site_url']);
				unset($this->data['SiteConfig']['ssl_url']);
				unset($this->data['SiteConfig']['admin_ssl']);
				unset($this->data['SiteConfig']['mobile']);
				unset($this->data['SiteConfig']['smartphone']);
				

				// DBに保存
				if($this->SiteConfig->saveKeyValue($this->data)) {

					$this->setMessage('システム設定を保存しました。');

					// 環境設定を保存
					$this->BcManager->setInstallSetting('debug', $mode);
					$this->BcManager->setInstallSetting('BcEnv.siteUrl', "'".$siteUrl."'");
					$this->BcManager->setInstallSetting('BcEnv.sslUrl', "'".$sslUrl."'");
					$this->BcManager->setInstallSetting('BcApp.adminSsl', ($adminSsl)? 'true' : 'false');
					$this->BcManager->setInstallSetting('BcApp.mobile', ($mobile)? 'true' : 'false');
					$this->BcManager->setInstallSetting('BcApp.smartphone', ($smartphone)? 'true' : 'false');
					
					if($this->BcManager->smartUrl() != $smartUrl) {
						$this->BcManager->setSmartUrl($smartUrl);
					}

					// キャッシュをクリア
					if($this->data['SiteConfig']['maintenance'] || 
							($this->siteConfigs['google_analytics_id'] != $this->data['SiteConfig']['google_analytics_id'])){
						clearViewCache();
					}

					// リダイレクト
					if($this->BcManager->smartUrl() != $smartUrl) {
						$adminPrefix = Configure::read('Routing.admin');
						if($smartUrl){
							$redirectUrl = $this->BcManager->getRewriteBase('/'.$adminPrefix.'/site_configs/form');
						}else{
							$redirectUrl = $this->BcManager->getRewriteBase('/index.php/'.$adminPrefix.'/site_configs/form');
						}
						header('Location: '.FULL_BASE_URL.$redirectUrl);
						exit();
					}else{
						$this->redirect(array('action' => 'form'));
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

		if(BC_DEPLOY_PATTERN != 3) {
			$htaccess1 = ROOT.DS.'.htaccess';
		} else {
			$htaccess1 = docRoot().DS.'.htaccess';
		}
		$htaccess2 = WWW_ROOT.'.htaccess';

		$writableHtaccess = is_writable($htaccess1);
		if($htaccess1 != $htaccess2) {
			$writableHtaccess2 = is_writable($htaccess2);
		} else{
			$writableHtaccess2 = true;
		}
		$baseUrl = str_replace('/index.php', '', BC_BASE_URL);

		if($writableInstall && $writableHtaccess && $writableHtaccess2 && $rewriteInstalled !== false){
			$smartUrlChangeable = true;
		} else {
			$smartUrlChangeable = false;
		}

		$UserGroup = ClassRegistry::init('UserGroup');
		$userGroups = $UserGroup->find('list', array('fields' => array('UserGroup.id', 'UserGroup.title')));
		
		$this->set('userGroups', $userGroups);
		$this->set('rewriteInstalled', $rewriteInstalled);
		$this->set('writableInstall', $writableInstall);
		$this->set('writableHtaccess', $writableHtaccess);
		$this->set('writableHtaccess2', $writableHtaccess2);
		$this->set('baseUrl', $baseUrl);
		$this->set('smartUrlChangeable', $smartUrlChangeable);
		$this->subMenuElements = array('site_configs');
		$this->pageTitle = 'サイト基本設定';
		$this->help = 'site_configs_form';
		
	}
/**
 * キャッシュファイルを全て削除する
 * 
 * @return void
 * @access public
 */
	function admin_del_cache() {
		
		clearAllCache();
		$this->setMessage('サーバーキャッシュを削除しました。');
		$this->redirect(array('action' => 'form'));
		
	}
/**
 * [ADMIN] PHPINFOを表示する
 * 
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
		$driver = preg_replace('/^bc_/', '', $db->config['driver']);
		$this->set('driver',$drivers[$driver]);
		$this->set('smartUrl',$smartUrl);
		$this->set('baserVersion',$this->siteConfigs['version']);
		$this->set('cakeVersion', Configure::version());
		$this->subMenuElements = array('site_configs');

	}
/**
 * [ADMIN] PHP INFO
 * 
 * @return void
 * @access public
 */
	function admin_phpinfo() {
		$this->layout = 'empty';
	}
/**
 * サイト基本設定データを取得する
 *
 * @return void
 * @access protected
 */
	function _getSiteConfigData() {

		$data['SiteConfig'] = $this->siteConfigs;
		$data['SiteConfig']['mode'] = Configure::read('debug');
		$data['SiteConfig']['smart_url'] = $this->BcManager->smartUrl();
		$data['SiteConfig']['site_url'] = Configure::read('BcEnv.siteUrl');
		$data['SiteConfig']['ssl_url'] = Configure::read('BcEnv.sslUrl');
		$data['SiteConfig']['admin_ssl'] = Configure::read('BcApp.adminSsl');
		$data['SiteConfig']['mobile'] = Configure::read('BcApp.mobile');
		$data['SiteConfig']['smartphone'] = Configure::read('BcApp.smartphone');
		if(is_null($data['SiteConfig']['mobile'])) {
			$data['SiteConfig']['mobile'] = false;
		}
		if(!isset($data['SiteConfig']['linked_pages_mobile'])) {
			$data['SiteConfig']['linked_pages_mobile'] = 0;
		}
		if(!isset($data['SiteConfig']['linked_pages_smartphone'])) {
			$data['SiteConfig']['linked_pages_smartphone'] = 0;
		}
		return $data;

	}

}
