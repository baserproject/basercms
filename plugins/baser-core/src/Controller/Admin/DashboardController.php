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
use Cake\Core\Plugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
     * @noTodo
     * @unitTest
     */
    public function index()
    {
        $this->setTitle(__d('baser', 'ダッシュボード'));
        $panels = [];
        $plugins = Plugin::loaded();
        if ($plugins) {
            foreach($plugins as $plugin) {
                $templates = BcUtil::getTemplateList('element/Admin/Dashboard', $plugin, $this->siteConfigs['theme']);
                $panels[$plugin] = $templates;
            }
        }
        $this->set('panels', $panels);
        $this->setHelp('dashboard_index');
    }

}
