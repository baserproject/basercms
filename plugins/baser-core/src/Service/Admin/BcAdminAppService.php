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

namespace BaserCore\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;

/**
 * BcAdminAppService
 */
class BcAdminAppService implements BcAdminAppServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 管理画面全体で必要な変数を取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAll(): array
    {
        if(!BcUtil::isInstalled()) return [];
        return [
            'permissionMethodList' => $this->getService(PermissionsServiceInterface::class)->getMethodList(),
            'permissionAuthList' => $this->getService(PermissionsServiceInterface::class)->getAuthList(),
            'useAdminSideBanner' => $this->getService(SiteConfigsServiceInterface::class)->getValue('admin_side_banner'),
        ];
    }
}
