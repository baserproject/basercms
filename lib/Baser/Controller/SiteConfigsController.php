<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class SiteConfigsController
 *
 * サイト設定コントローラー
 *
 * @package Baser.Controller
 * @property BcManagerComponent $BcManager
 * @property SiteConfig $SiteConfig
 * @property Site $Site
 * @property CakeRequest $request
 */
class SiteConfigsController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'SiteConfigs';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['SiteConfig', 'Page', 'Site'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcManager'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * ヘルパー
	 * @var array
	 */
	public $helpers = ['BcForm', 'BcPage'];

	/**
	 * beforeFilter
	 */
	public function beforeFilter()
	{
		// @deprecated 5.0.0 since 4.0.0 ajax_get_token は、BcFormController に移行した為、次のバージョンで削除
		$this->BcAuth->allow('admin_ajax_credit', 'jquery_base_url', 'ajax_get_token');
		parent::beforeFilter();
		$this->crumbs = [['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']]];
	}

	/**
	 * [ADMIN] サイト基本設定
	 */
	public function admin_form()
	{
		$writableInstall = is_writable(APP . 'Config' . DS . 'install.php');

		if (empty($this->request->data)) {
			$this->request->data = $this->_getSiteConfigData();
		} else {
			$this->SiteConfig->set($this->request->data);

			if (!$this->SiteConfig->validates()) {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			} else {

				$mode = 0;
				$siteUrl = $sslUrl = '';
				if (isset($this->request->data['SiteConfig']['mode'])) {
					$mode = $this->request->data['SiteConfig']['mode'];
				}
				if ($mode > 0) {
					clearAllCache();
				} else {
					clearViewCache();
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

				$adminSsl = $this->request->data['SiteConfig']['admin_ssl'];
				if ($this->request->data['SiteConfig']['use_site_device_setting'] === "0" && $this->SiteConfig->isChange('use_site_device_setting', "0")) {
					$this->Site->resetDevice();
				}
				if ($this->request->data['SiteConfig']['use_site_lang_setting'] === "0" && $this->SiteConfig->isChange('use_site_lang_setting', "0")) {
					$this->Site->resetLang();
				}
				if (Configure::read('BcSite.admin_theme') !== $this->request->data['SiteConfig']['admin_theme']) {
					Configure::write('BcSite.admin_theme', $this->request->data['SiteConfig']['admin_theme']);
					$this->BcManager->deleteAdminAssets();
					$this->BcManager->deployAdminAssets();
				}
				unset($this->request->data['SiteConfig']['id']);
				unset($this->request->data['SiteConfig']['mode']);
				unset($this->request->data['SiteConfig']['site_url']);
				unset($this->request->data['SiteConfig']['ssl_url']);
				unset($this->request->data['SiteConfig']['admin_ssl']);
				unset($this->request->data['SiteConfig']['mobile']);
				unset($this->request->data['SiteConfig']['smartphone']);

				// DBに保存
				if ($this->SiteConfig->saveKeyValue($this->request->data)) {

					$ContentFolder = ClassRegistry::init('ContentFolder');
					$ContentFolder->saveSiteRoot(0, [
						'title' => $this->request->data['SiteConfig']['name']
					]);

					$this->BcMessage->setInfo(__d('baser', 'システム設定を保存しました。'));

					// 環境設定を保存
					if ($writableInstall) {
						$this->BcManager->setInstallSetting('debug', $mode);
						$this->BcManager->setInstallSetting('BcEnv.siteUrl', "'" . $siteUrl . "'");
						$this->BcManager->setInstallSetting('BcEnv.sslUrl', "'" . $sslUrl . "'");
						$this->BcManager->setInstallSetting('BcApp.adminSsl', ($adminSsl)? 'true' : 'false');
					}

					// キャッシュをクリア
					if ($this->request->data['SiteConfig']['maintenance'] ||
						($this->siteConfigs['google_analytics_id'] != $this->request->data['SiteConfig']['google_analytics_id'])
					) {
						clearViewCache();
					}

					$this->redirect(['action' => 'form']);
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

		$UserGroup = ClassRegistry::init('UserGroup');
		$userGroups = $UserGroup->find('list', ['fields' => ['UserGroup.id', 'UserGroup.title']]);

		$disableSettingInstallSetting = [];
		if (!$writableInstall) {
			$disableSettingInstallSetting = ['disabled' => 'disabled'];
		}
		$themes = array_merge(['' => 'admin-second'], BcUtil::getAdminThemeList());
		$this->set(compact(
			'baseUrl', 'userGroups', 'rewriteInstalled', 'writableInstall', 'writableHtaccess',
			'writableHtaccess2', 'disableSettingInstallSetting', 'themes'
		));
		$this->subMenuElements = ['site_configs'];
		$this->pageTitle = __d('baser', 'サイト基本設定');
		$this->help = 'site_configs_form';
	}

	/**
	 * キャッシュファイルを全て削除する
	 */
	public function admin_del_cache()
	{
		$this->_checkReferer();
		clearAllCache();
		$this->BcMessage->setInfo(__d('baser', 'サーバーキャッシュを削除しました。'));
		$this->redirect($this->referer());
	}

	/**
	 * [ADMIN] PHPINFOを表示する
	 */
	public function admin_info()
	{

		$this->pageTitle = __d('baser', '環境情報');
		$datasources = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
		$db = ConnectionManager::getDataSource('default');
		list($type, $name) = explode('/', $db->config['datasource'], 2);
		$datasource = preg_replace('/^bc/', '', strtolower($name));
		$this->set('datasource', @$datasources[$datasource]);
		$this->set('baserVersion', $this->siteConfigs['version']);
		$this->set('cakeVersion', Configure::version());
		$this->subMenuElements = ['site_configs', 'tools'];
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'index']],
			['name' => __d('baser', 'ユーティリティ'), 'url' => ['controller' => 'tools', 'action' => 'index']]
		];

	}

	/**
	 * [ADMIN] PHP INFO
	 */
	public function admin_phpinfo()
	{
		$this->layout = 'empty';
	}

	/**
	 * サイト基本設定データを取得する
	 */
	protected function _getSiteConfigData()
	{
		$data['SiteConfig'] = $this->siteConfigs;
		$data['SiteConfig']['mode'] = Configure::read('debug');
		$data['SiteConfig']['site_url'] = Configure::read('BcEnv.siteUrl');
		$data['SiteConfig']['ssl_url'] = Configure::read('BcEnv.sslUrl');
		$data['SiteConfig']['admin_ssl'] = (int)Configure::read('BcApp.adminSsl');
		if (!isset($data['SiteConfig']['editor_enter_br'])) {
			$data['SiteConfig']['editor_enter_br'] = 0;
		}
		return $data;
	}

	/**
	 * メールの送信テストを実行する
	 */
	public function admin_check_sendmail()
	{

		if (empty($this->request->data['SiteConfig'])) {
			$this->ajaxError(500, __d('baser', 'データが送信できませんでした。'));
		}
		$this->siteConfigs = $this->request->data['SiteConfig'];
		if (!$this->sendMail(
			$this->siteConfigs['email'], __d('baser', 'メール送信テスト'),
			sprintf('%s からのメール送信テストです。', $this->siteConfigs['formal_name']) . "\n" . Configure::read('BcEnv.siteUrl')
		)) {
			$this->ajaxError(500, __d('baser', 'ログを確認してください。'));
			return;
		}

		exit();
	}

	/**
	 * クレジット表示用データをレンダリング
	 */
	public function admin_ajax_credit()
	{

		$this->layout = 'ajax';
		Configure::write('debug', 0);

		$specialThanks = [];
		if (!Configure::read('Cache.disable') && Configure::read('debug') == 0) {
			$specialThanks = Cache::read('special_thanks', '_cake_env_');
		}

		if ($specialThanks) {
			$json = json_decode($specialThanks);
		} else {
			try {
				$json = file_get_contents(Configure::read('BcApp.specialThanks'), true);
			} catch (Exception $ex) {
			}
			if ($json) {
				if (!Configure::read('Cache.disable')) {
					Cache::write('special_thanks', $json, '_cake_env_');
				}
				$json = json_decode($json);
			} else {
				$json = null;
			}

		}

		if ($json == false) {
			$this->ajaxError(500, __d('baser', 'スペシャルサンクスデータが取得できませんでした。'));
		}
		$this->set('credits', $json);

	}

	/**
	 * admin用Token取得アクション
	 *
	 * @return string
	 * @deprecated 5.0.0 since 4.0.0 ajax_get_token は、BcFormController に移行した為、次のバージョンで削除
	 */
	public function admin_ajax_get_token()
	{
		$this->autoRender = false;
		return $this->getToken();
	}

	/**
	 * セキュリティトークンを取得する
	 *
	 * @return mixed
	 * @deprecated 5.0.0 since 4.0.0 ajax_get_token は、BcFormController に移行した為、次のバージョンで削除
	 */
	public function ajax_get_token()
	{
		return $this->setAction(('admin_ajax_get_token'));
	}

}
