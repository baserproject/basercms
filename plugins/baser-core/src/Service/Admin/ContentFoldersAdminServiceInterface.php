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
use Cake\Datasource\EntityInterface;

/**
 * Interface ContentFoldersAdminServiceInterface
 */
interface ContentFoldersAdminServiceInterface
{

    /**
     * 編集画面に必要なデータを取得する
     * @param int $id
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $contentFolder): array;

}
