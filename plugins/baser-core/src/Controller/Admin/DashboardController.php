<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use BaserCore\Service\DashboardAdminServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class DashboardController
 * @uses DashboardController
 */
class DashboardController extends BcAdminAppController
{

    /**
     * [ADMIN] 管理者ダッシュボードページを表示する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DashboardAdminServiceInterface $service)
    {
        $this->setTitle(__d('baser', 'ダッシュボード'));
        $this->set($service->getViewVarsForIndex(5));
    }

}
