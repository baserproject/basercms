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

use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PermissionGroupsAdminServiceInterface
 */
interface PermissionGroupsAdminServiceInterface
{

    /**
     * 一覧用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(int $userGroupId, ServerRequest $request): array;

    /**
     * 新規登録、編集画面用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param EntityInterface $entity
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForForm(int $userGroupId, EntityInterface $entity): array;

}
