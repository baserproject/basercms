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
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

/**
 * PermissionsAdminService
 */
class PermissionsAdminService extends PermissionsService implements PermissionsAdminServiceInterface
{

    /**
     * Traint
     */
    use BcContainerTrait;

    /**
     * アクセスルール一覧用の View 変数を取得する
     *
     * @param ServerRequest $request
     * @param int $userGroupId
     * @return array
     */
    public function getViewVarsForIndex(ServerRequest $request, int $userGroupId)
    {
        $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
        /** @var PermissionGroupsService $permissionGroupsService */
        $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
        if($userGroupId) {
            $currentUserGroup = $this->Permissions->UserGroups->get($userGroupId);
        }
		return [
            'currentUserGroup' => $currentUserGroup?? null,
            'userGroupTitle' => $currentUserGroup->title?? __d('baser_core', 'ゲスト'),
            'userGroupId' => $currentUserGroup->id ?? "0",
            'permissionGroups' => $permissionGroupsService->getIndex($userGroupId, [])->all(),
            'permissions' => $this->getIndex($request->getQueryParams())->all(),
            'sortmode' => $request->getQuery('sortmode')
        ];
    }

    /**
     * アクセスルール新規登録画面用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param EntityInterface $entity
     * @return array
     */
    public function getViewVarsForAdd(int $userGroupId, EntityInterface $entity)
    {
        if($userGroupId) {
            $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
            $currentUserGroup = $userGroupsService->get($userGroupId);
        }

        $permissionGroupsTable = TableRegistry::getTableLocator()->get('BaserCore.PermissionGroups');
        return [
            'permissionGroups' => $permissionGroupsTable->find()->all(),
            'permission' => $entity,
            'userGroupId' => $currentUserGroup?? "0",
            'userGroupTitle' => __d('baser_core', 'ゲスト')
        ];
    }

    /**
     * アクセスルール編集画面用の View 変数を取得する
     *
     * @param int $userGroupId
     * @param EntityInterface $entity
     * @return array
     */
    public function getViewVarsForEdit(int $userGroupId, EntityInterface $entity)
    {
        if($userGroupId) {
            $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
            $currentUserGroup = $userGroupsService->get($userGroupId);
        }

        $permissionGroupsTable = TableRegistry::getTableLocator()->get('BaserCore.PermissionGroups');
        return [
            'permissionGroups' => $permissionGroupsTable->find()->all(),
            'permission' => $entity,
            'userGroupId' => $currentUserGroup?? "0",
            'userGroupTitle' => __d('baser_core', 'ゲスト')
        ];
    }

}
