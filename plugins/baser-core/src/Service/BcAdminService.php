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

namespace BaserCore\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;

/**
 * ContentFoldersAdminService
 */
class BcAdminService implements BcAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 管理画面全体で必要な変数を取得
     * @return string[]
     */
    public function getViewVarsForAll(): array
    {
        return [
            'permissionMethodList' => $this->getService(PermissionsServiceInterface::class)->getMethodList(),
            'permissionAuthList' => $this->getService(PermissionsServiceInterface::class)->getAuthList()
        ];
    }
}
