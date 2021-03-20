<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcUtil;

/**
 * Class DashboardController
 * @package BaserCore\Controller\Admin
 */
class DashboardController extends BcAdminAppController
{

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['BaserCore.User', 'BaserCore.Page'];

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = [];

	/**
	 * [ADMIN] 管理者ダッシュボードページを表示する
	 *
	 * @return void
     * @checked
	 */
	public function index()
	{
	    $this->setTitle(__d('baser', 'ダッシュボード'));

	    // TODO 未実装のため代替措置
	    // >>>
	    return;
	    // <<<

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
