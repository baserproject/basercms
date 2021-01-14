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
 * Class DashboardController
 *
 * ダッシュボードコントローラー
 * 管理者ログインやメンバーログインのダッシュボードページを表示する
 *
 * @package Baser.Controller
 */
class DashboardController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Dashboard';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['User', 'Page'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcTime', 'Js'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = [];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * [ADMIN] 管理者ダッシュボードページを表示する
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->pageTitle = __d('baser', 'ダッシュボード');
		$panels = [];
		$panels['Core'] = BcUtil::getTemplateList('Elements/admin/dashboard', '', $this->siteConfigs['theme']);
		$plugins = CakePlugin::loaded();
		if ($plugins) {
			foreach($plugins as $plugin) {
				$templates = BcUtil::getTemplateList('Elements/admin/dashboard', $plugin, $this->siteConfigs['theme']);
				foreach($templates as $key => $template) {
					if (in_array($template, $panels['Core'])) {
						unset($templates[$key]);
					}
				}
				$panels[$plugin] = $templates;
			}
		}
		$this->set('panels', $panels);
		$this->help = 'dashboard_index';
	}

}
