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

use BaserCore\Service\PermissionGroupsService;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * PermissionGroupsAdminService
 */
class PermissionGroupsAdminService extends PermissionGroupsService implements PermissionGroupsAdminServiceInterface
{

    /**
     * 一覧用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(int $userGroupId, ServerRequest $request): array
    {
        return [
            'entities' => $this->getIndex($userGroupId, $request->getQueryParams()),
            'currentUserGroup' => $this->UserGroups->get($userGroupId)
        ];
    }

    /**
     * 編集画面用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param EntityInterface $entity
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForEdit(int $userGroupId, EntityInterface $entity): array
    {
        return [
            'entity' => $entity,
            'currentUserGroup' => $this->UserGroups->get($userGroupId)
        ];
    }

}
