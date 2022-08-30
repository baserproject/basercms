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

use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Model\Table\PermissionsTable;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use Authentication\Controller\Component\AuthenticationComponent;

/**
 * Class PermissionsController
 * @package BaserCore\Controller\Admin
 * @property UserGroupsTable $UserGroups
 * @property PermissionsTable $Permissions
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 */
class PermissionsController extends BcAdminAppController
{

	/**
	 * beforeFilter
     *
	 * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
        $this->loadModel('BaserCore.Permissions');
        $this->viewBuilder()->addHelpers(
            ['BcTime',
            // 'BcFreeze'
        ]);
        $this->Security->setConfig('unlockedActions', [
            'update_sort',
            'batch',
        ]);
	}

	/**
	 * アクセス制限設定の一覧を表示する
	 *
	 * @return void
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function index(PermissionsServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId = '')
	{
		$currentUserGroup = $userGroups->get($userGroupId);

        $this->request = $this->request->withQueryParams(array_merge(
            $this->getRequest()->getQueryParams(),
            ['user_group_id' => $userGroupId]
        ));
        $this->setViewConditions('Permission', ['default' => ['query' => [
            'sort' => 'sort',
            'direction' => 'asc',
        ]]]);

        $this->set('currentUserGroup', $currentUserGroup);
        $this->set('permissions', $permissionService->getIndex($this->request->getQueryParams()));

		$this->set('sortmode', $this->request->getQuery('sortmode'));
	}

	/**
	 * [ADMIN] 登録処理
     *
     * @param PermissionsServiceInterface $userService
     * @param UserGroupsServiceInterface $userGroups
     * @param int $userGroupId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function add(PermissionsServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId)
	{
		$currentUserGroup = $userGroups->get($userGroupId);
        if ($this->request->is('post')) {
            try {
                $permission = $permissionService->create($this->request->withData('user_group_id', $currentUserGroup->id)->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser', '新規アクセス制限設定「%s」を追加しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $permission = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set('permission', $permission ?? $permissionService->getNew($userGroupId));
        $this->set('currentUserGroup', $currentUserGroup);
	}

    /**
     * [ADMIN] 編集処理
     *
     * @param PermissionsServiceInterface $userService
     * @param UserGroupsServiceInterface $userGroups
     * @param int $userGroupId
     * @param int $permissionId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */

	public function edit(PermissionsServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId, $permissionId)
    {
		$currentUserGroup = $userGroups->get($userGroupId);
        $permission = $permissionService->get($permissionId);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $permission = $permissionService->update($permission, $this->request->withData('user_group_id', $currentUserGroup->id)->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を更新しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        $this->set('permission', $permission);
        $this->set('currentUserGroup', $currentUserGroup);
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
	public function delete(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $permissionName = $permission->name;
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $permissionService->delete($permissionId);
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を削除しました。'), $permissionName));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 複製処理
     *
     * @param PermissionsServiceInterface $userService
     * @param int $userGroupId
     * @param int $permissionId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
	public function copy(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($permissionService->copy($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を複製しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
	public function unpublish(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($permissionService->unpublish($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を無効にしました。'),
                    $permission->name));
            }
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
	public function publish(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($permissionService->publish($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を有効にしました。'),
                    $permission->name));
            }
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

}
