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

use BaserCore\Service\Admin\PermissionsAdminServiceInterface;
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
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;

/**
 * Class PermissionsController
 *
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
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
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
	 * アクセスルールの一覧を表示する
	 *
     * @param PermissionsServiceInterface $service
     * @param UserGroupsServiceInterface $userGroupsService
     * @param int $userGroupId
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function index(
	    PermissionsAdminServiceInterface $service,
	    $userGroupId = ''
	) {
        $this->request = $this->request->withQueryParams(array_merge(
            $this->getRequest()->getQueryParams(),
            ['user_group_id' => $userGroupId]
        ));

        $this->setViewConditions('Permission', ['default' => ['query' => [
            'sort' => 'sort',
            'direction' => 'asc',
            'permission_group_id' => null,
            'permission_group_type' => null
        ]]]);

        $request = $this->getRequest();
        $this->setRequest($request->withParsedBody($request->getQueryParams()));
        $this->set($service->getViewVarsForIndex($request, $userGroupId));
	}

    /**
     * [ADMIN] 登録処理
     *
     * @param PermissionsServiceInterface $service
     * @param UserGroupsServiceInterface $userGroupsService
     * @param int $userGroupId
     * @param int|null $permissionGroupId
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
	public function add(
	    PermissionsAdminServiceInterface $service,
	    int $userGroupId,
	    int $permissionGroupId = null
	) {
        if ($this->request->is('post')) {
            try {
                $permission = $service->create($this->request->withData('user_group_id', $userGroupId)->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', '新規アクセスルール「%s」を追加しました。'), $permission->name));
                if($permissionGroupId) {
                    return $this->redirect(['controller' => 'PermissionGroups', 'action' => 'edit', $userGroupId, $permissionGroupId]);
                } else {
                    return $this->redirect(['action' => 'index', $userGroupId]);
                }
            } catch (PersistenceFailedException $e) {
                $permission = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($service->getViewVarsForAdd(
            $userGroupId,
            $permission ?? $service->getNew($userGroupId, $permissionGroupId)
        ));
	}

    /**
     * [ADMIN] 編集処理
     *
     * @param PermissionsServiceInterface $userService
     * @param UserGroupsServiceInterface $userGroupsService
     * @param int $userGroupId
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
	public function edit(
	    PermissionsAdminServiceInterface $service,
	    $userGroupId,
	    $permissionId,
	    $permissionGroupId = null
	) {
        $permission = $service->get($permissionId);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $permission = $service->update($permission, $this->request->withData('user_group_id', $userGroupId)->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'アクセスルール「%s」を更新しました。'), $permission->name));
                if($permissionGroupId) {
                    return $this->redirect(['controller' => 'PermissionGroups', 'action' => 'edit', $userGroupId, $permissionGroupId]);
                } else {
                    return $this->redirect(['action' => 'index', $userGroupId]);
                }
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }

        $this->set($service->getViewVarsForEdit(
            $userGroupId,
            $permission
        ));
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param PermissionsServiceInterface $service
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
	public function delete(PermissionsServiceInterface $service, $permissionId)
    {
        $permission = $service->get($permissionId);
        $permissionName = $permission->name;
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $service->delete($permissionId);
            $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'アクセスルール「%s」を削除しました。'), $permissionName));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 複製処理
     *
     * @param PermissionsServiceInterface $userService
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
	public function copy(PermissionsServiceInterface $service, $permissionId)
    {
        $permission = $service->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($service->copy($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'アクセスルール「%s」を複製しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            }
            $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param PermissionsServiceInterface $service
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
	public function unpublish(PermissionsServiceInterface $service, $permissionId)
    {
        $permission = $service->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($service->unpublish($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'アクセスルール「%s」を無効にしました。'),
                    $permission->name));
            }
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param PermissionsServiceInterface $service
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
	public function publish(PermissionsServiceInterface $service, $permissionId)
    {
        $permission = $service->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($service->publish($permissionId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'アクセスルール「%s」を有効にしました。'),
                    $permission->name));
            }
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

}
