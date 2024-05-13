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
     * @unitTest
     */
    public function getViewVarsForIndex(int $userGroupId, ServerRequest $request): array
    {
        if($userGroupId) {
            $currentUserGroup = $this->UserGroups->get($userGroupId);
        }
        return [
            'entities' => $this->getIndex($userGroupId, array_merge(
                ['permission_amount' => true],
                $request->getData()
            )),
            'userGroupId' => $currentUserGroup->id ?? "0"
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
     * @unitTest
     */
    public function getViewVarsForForm(int $userGroupId, EntityInterface $entity): array
    {
        if($userGroupId) {
            $currentUserGroup = $this->UserGroups->get($userGroupId);
        }
        $isDeletable = false;
        if ($entity->id) {
            $isDeletable = !$this->PermissionGroups->Permissions
                ->exists(['Permissions.permission_group_id' => $entity->id]);
        }
        return [
            'entity' => $entity,
            'userGroupTitle' => $currentUserGroup->title?? __d('baser_core', 'ゲスト'),
            'userGroupId' => $currentUserGroup->id ?? '0',
            'isDeletable' => $isDeletable,
        ];
    }

}
