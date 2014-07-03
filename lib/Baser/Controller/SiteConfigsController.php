<?php

/**
 * サイト設定コントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * サイト設定コントローラー
 *
 * @package Baser.Controller
 */
class SiteConfigsController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'SiteConfigs';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('SiteConfig', 'Menu', 'Page');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcManager');

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * ヘルパー
 * @var array
 * @access public
 */
	public $helpers = array('BcForm', 'BcPage');

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')));

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * [ADMIN] サイト基本設定
 *
 * @return void
 * @access public
 */
	public function admin_form() {
		$writableInstall = is_writable(APP . 'Config' . DS . 'install.php');

		if (empty($this->request->data)) {

			$this->request->data = $this->_getSiteConfigData();
		} else {
			$this->SiteConfig->set($this->request->data);

			if (!$this->SiteConfig->validates()) {

				$this->setMessage('入力エラーです。内容を修正してください。', true);
			} else {

				$mode = 0;
				$smartUrl = false;
				$siteUrl = $sslUrl = '';
				if (isset($this->request->data['SiteConfig']['mode'])) {
					$mode = $this->request->data['SiteConfig']['mode'];
				}
				if (isset($this->request->data['SiteConfig']['smart_url'])) {
					$smartUrl = $this->request->data['SiteConfig']['smart_url'];
				}
				if (isset($this->request->data['SiteConfig']['ssl_url'])) {
					$siteUrl = $this->request->data['SiteConfig']['site_url'];
					if (!preg_match('/\/$/', $siteUrl)) {
						$siteUrl .= '/';
					}
				}
				if (isset($this->request->data['SiteConfig']['ssl_url'])) {
					$sslUrl = $this->request->data['SiteConfig']['ssl_url'];
					if ($sslUrl && !preg_match('/\/$/', $sslUrl)) {
						$sslUrl .= '/';
					}
				}

				$adminSsl = @$this->request->data['SiteConfig']['admin_ssl'];
				$mobile = @$this->request->data['SiteConfig']['mobile'];
				$smartphone = @$this->request->data['SiteConfig']['smartphone'];

				unset($this->request->data['SiteConfig']['id']);
				unset($this->request->data['SiteConfig']['mode']);
				unset($this->request->data['SiteConfig']['smart_url']);
				unset($this->request->data['SiteConfig']['site_url']);
				unset($this->request->data['SiteConfig']['ssl_url']);
				unset($this->request->data['SiteConfig']['admin_ssl']);
				unset($this->request->data['SiteConfig']['mobile']);
				unset($this->request->data['SiteConfig']['smartphone']);

				// DBに保存
				if ($this->SiteConfig->saveKeyValue($this->request->data)) {

					$this->setMessage('システム設定を保存しました。');

					// 環境設定を保存
					if ($writableInstall) {
						$this->BcManager->setInstallSetting('debug', $mode);
						$this->BcManager->setInstallSetting('BcEnv.siteUrl', "'" . $siteUrl . "'");
						$this->BcManager->setInstallSetting('BcEnv.sslUrl', "'" . $sslUrl . "'");
						$this->BcManager->setInstallSetting('BcApp.adminSsl', ($adminSsl) ? 'true' : 'false');
						$this->BcManager->setInstallSetting('BcApp.mobile', ($mobile) ? 'true' : 'false');
						$this->BcManager->setInstallSetting('BcApp.smartphone', ($smartphone) ? 'true' : 'false');
					}

					if ($this->BcManager->smartUrl() != $smartUrl) {
						$this->BcManager->setSmartUrl($smartUrl);
					}

					// キャッシュをクリア
					if ($this->request->data['SiteConfig']['maintenance'] ||
						($this->siteConfigs['google_analytics_id'] != $this->request->data['SiteConfig']['google_analytics_id'])) {
						clearViewCache();
					}

					// リダイレクト
					if ($this->BcManager->smartUrl() != $smartUrl) {
						$adminPrefix = Configure::read('Routing.prefixes.0');
						if ($smartUrl) {
							$redirectUrl = $this->BcManager->getRewriteBase('/' . $adminPrefix . '/site_configs/form');
						} else {
							$redirectUrl = $this->BcManager->getRewriteBase('/index.php/' . $adminPrefix . '/site_configs/form');
						}
						header('Location: ' . FULL_BASE_URL . $redirectUrl);
						exit();
					} else {
						$this->redirect(array('action' => 'form'));
					}
				}
			}
		}

		/* スマートURL関連 */
		$apachegetmodules = function_exists('apache_get_modules');
		if ($apachegetmodules) {
			$rewriteInstalled = in_array('mod_rewrite', apache_get_modules());
		} else {
			$rewriteInstalled = -1;
		}

		if (BC_DEPLOY_PATTERN != 3) {
			$htaccess1 = ROOT . DS . '.htaccess';
		} else {
			$htaccess1 = docRoot() . DS . '.htaccess';
		}
		$htaccess2 = WWW_ROOT . '.htaccess';

		$writableHtaccess = is_writable($htaccess1);
		if ($htaccess1 != $htaccess2) {
			$writableHtaccess2 = is_writable($htaccess2);
		} else {
			$writableHtaccess2 = true;
		}
		$baseUrl = str_replace('/index.php', '', BC_BASE_URL);

		if ($writableInstall && $writableHtaccess && $writableHtaccess2 && $rewriteInstalled !== false) {
			$smartUrlChangeable = true;
		} else {
			$smartUrlChangeable = false;
		}

		$UserGroup = ClassRegistry::init('UserGroup');
		$userGroups = $UserGroup->find('list', array('fields' => array('UserGroup.id', 'UserGroup.title')));

		$disableSettingSmartUrl = array();
		$disableSettingInstallSetting = array();
		if (!$smartUrlChangeable) {
			$disableSettingSmartUrl = array('disabled' => 'disabled');
		}
		if (!$writableInstall) {
			$disableSettingInstallSetting = array('disabled' => 'disabled');
		}

		$this->set(compact(
				'baseUrl', 'userGroups', 'rewriteInstalled', 'writableInstall', 'writableHtaccess', 'writableHtaccess2', 'smartUrlChangeable', 'disableSettingSmartUrl', 'disableSettingInstallSetting'
		));

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
	public function admin_del_cache() {
		clearAllCache();
		$this->setMessage('サーバーキャッシュを削除しました。');
		$this->redirect($this->referer());
	}

/**
 * [ADMIN] PHPINFOを表示する
 * 
 * @return void
 * @access public
 */
	public function admin_info() {
		if (!empty($this->siteConfigs['demo_on'])) {
			$this->notFound();
		}

		$this->pageTitle = '環境情報';

		$smartUrl = 'ON';
		if (Configure::read('App.baseUrl')) {
			$smartUrl = 'OFF';
		}

		$datasources = array('csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL');
		$db = ConnectionManager::getDataSource('baser');
		list($type, $name) = explode('/', $db->config['datasource'], 2);
		$datasource = preg_replace('/^bc/', '', strtolower($name));
		$this->set('datasource', @$datasources[$datasource]);
		$this->set('smartUrl', $smartUrl);
		$this->set('baserVersion', $this->siteConfigs['version']);
		$this->set('cakeVersion', Configure::version());
		$this->subMenuElements = array('site_configs');
	}

/**
 * [ADMIN] PHP INFO
 * 
 * @return void
 * @access public
 */
	public function admin_phpinfo() {
		$this->layout = 'empty';
	}

/**
 * サイト基本設定データを取得する
 *
 * @return void
 * @access protected
 */
	protected function _getSiteConfigData() {
		$data['SiteConfig'] = $this->siteConfigs;
		$data['SiteConfig']['mode'] = Configure::read('debug');
		$data['SiteConfig']['smart_url'] = $this->BcManager->smartUrl();
		$data['SiteConfig']['site_url'] = Configure::read('BcEnv.siteUrl');
		$data['SiteConfig']['ssl_url'] = Configure::read('BcEnv.sslUrl');
		$data['SiteConfig']['admin_ssl'] = (int)Configure::read('BcApp.adminSsl');
		$data['SiteConfig']['mobile'] = Configure::read('BcApp.mobile');
		$data['SiteConfig']['smartphone'] = Configure::read('BcApp.smartphone');
		if (is_null($data['SiteConfig']['mobile'])) {
			$data['SiteConfig']['mobile'] = false;
		}
		if (!isset($data['SiteConfig']['linked_pages_mobile'])) {
			$data['SiteConfig']['linked_pages_mobile'] = 0;
		}
		if (!isset($data['SiteConfig']['linked_pages_smartphone'])) {
			$data['SiteConfig']['linked_pages_smartphone'] = 0;
		}
		if (!isset($data['SiteConfig']['editor_enter_br'])) {
			$data['SiteConfig']['editor_enter_br'] = 0;
		}
		return $data;
	}

}
