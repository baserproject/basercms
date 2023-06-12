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

namespace BcCustomContent\Service\Admin;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcSiteConfig;
use BcCustomContent\Service\CustomContentsService;
use Cake\Datasource\EntityInterface;

/**
 * CustomContentsAdminService
 */
class CustomContentsAdminService extends CustomContentsService implements CustomContentsAdminServiceInterface
{

    /**
     * カスタムコンテンツ編集画面用の View 変数を取得する
     *
     * @param EntityInterface $entity
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $entity)
    {
        return [
            'entity' => $entity,
            'customTables' => $this->getControlSource('custom_table_id'),
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br')
        ];
    }

}
