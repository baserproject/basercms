<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
// use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
// use Cake\Controller\ComponentRegistry;
use BaserCore\Service\SiteConfigsTrait;
// use Cake\Core\Exception\Exception;
// use Cake\Datasource\Exception\RecordNotFoundException;
// use Cake\Event\Event;
// use Cake\Event\EventInterface;
// use Cake\Event\EventManagerInterface;
// use Cake\Http\ServerRequest;
// use Cake\Routing\Router;
// use Cake\Http\Response;
// use Cake\Http\Exception\ForbiddenException;
// use Cake\Http\Cookie\Cookie;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Model\Table\PermissionsTable;
use BaserCore\Service\PermissionServiceInterface;
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
     * SiteConfigsTrait
     */
    use SiteConfigsTrait;

    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

	/**
	 * beforeFilter
     *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
        $this->loadModel('BaserCore.Permissions');
        $this->viewBuilder()->setHelpers(
            ['BcTime',
            // 'BcFreeze'
        ]);

		if ($this->request->getParam('prefix') === 'admin') {
			$this->set('usePermission', true);
		}
	}

	/**
	 * アクセス制限設定の一覧を表示する
	 *
	 * @return void
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function index(PermissionServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId = '')
	{
		$currentUserGroup = $userGroups->get($userGroupId);

        $this->request->withQueryParams(['user_group_id' => $userGroupId]);
        $this->setViewConditions('Permission', ['default' => ['query' => [
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        $this->set('currentUserGroup', $currentUserGroup);
        $this->set('permissions', $permissionService->getIndex($this->request->getQueryParams()));

		$this->_setAdminIndexViewData();
	}

	/**
	 * 一覧の表示用データをセットする
	 *
	 * @return void
	 */
	protected function _setAdminIndexViewData()
	{
		$this->set('sortmode', $this->request->getParam('sortmode'));
	}

	/**
	 * [ADMIN] 登録処理
     *
     * @param PermissionServiceInterface $userService
     * @param UserGroupsServiceInterface $userGroups
     * @param int $userGroupId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function add(PermissionServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId)
	{
		$currentUserGroup = $userGroups->get($userGroupId);
        if ($this->request->is('post')) {
            $permission = $permissionService->create($this->request->withData('user_group_id', $currentUserGroup->id)->getData());
            if (empty($permission->getErrors()) === true) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', '新規アクセス制限設定「%s」を追加しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $permission = $permissionService->getNew($userGroupId);
        }
        $this->set('permission', $permission);
        $this->set('currentUserGroup', $currentUserGroup);
	}

    /**
     * [ADMIN] 編集処理
     *
     * @param PermissionServiceInterface $userService
     * @param UserGroupsServiceInterface $userGroups
     * @param int $userGroupId
     * @param int $permissionId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */

	public function edit(PermissionServiceInterface $permissionService, UserGroupsServiceInterface $userGroups, $userGroupId, $permissionId)
    {
		$currentUserGroup = $userGroups->get($userGroupId);
        $permission = $permissionService->get($permissionId);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $permissionService->update($permission, $this->request->withData('user_group_id', $currentUserGroup->id)->getData());
            if (empty($permission->getErrors()) === true) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を更新しました。'), $permission->name));
                return $this->redirect(['action' => 'index', $userGroupId]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }

        $this->set('permission', $permission);
        $this->set('currentUserGroup', $currentUserGroup);
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param $ids
     * @return boolean
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                // メッセージ用にデータを取得
                $post = $this->Permission->read(null, $id);
                /* 削除処理 */
                if ($this->Permission->delete($id)) {
                    $message = sprintf(__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']);
                }
            }
        }
        return true;
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
	public function delete(PermissionServiceInterface $permissionService, $permissionId)
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
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function admin_ajax_add()
	{
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			exit;
		}

// TODO 現在 admin 固定、今後、mypage 等にも対応する
        $authPrefix = 'admin';
        $this->request = $this->request->withData('Permission.url', '/' . $authPrefix . '/' . $this->request->getData('Permission.url'));
        $this->request = $this->request->withData('Permission.no', $this->Permission->getMax('no', ['user_group_id' => $this->request->getData('Permission.user_group_id')]) + 1);
        $this->request = $this->request->withData('Permission.sort', $this->Permission->getMax('sort', ['user_group_id' => $this->request->getData('Permission.user_group_id')]) + 1);
        $this->request = $this->request->withData('Permission.status', true);
        $this->Permission->create($this->request->data);
        if (!$this->Permission->save()) {
            $this->ajaxError(500, $this->Page->validationErrors);
            exit;
        }

        $this->Permission->saveDbLog(
            sprintf(
                __d('baser', '新規アクセス制限設定「%s」を追加しました。'), $this->request->getData('Permission.name')
            )
        );
        exit(true);
    }

    /**
     * 並び替えを更新する [AJAX]
     *
     * @access    public
     * @param $userGroupId
     * @return void
     */
    public function admin_ajax_update_sort($userGroupId)
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            exit;
        }

        $conditions = [
            'Permission.user_group_id' => $userGroupId
        ];
        if (!$this->Permission->changeSort($this->request->getData('Sort.id'), $this->request->getData('Sort.offset'), $conditions)) {
            $this->ajaxError(500, $this->Permission->validationErrors);
            exit;
        }
        echo true;
    }


    /**
     * [ADMIN] 複製処理
     *
     * @param PermissionServiceInterface $userService
     * @param int $userGroupId
     * @param int $permissionId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
	public function copy(PermissionServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $permissionService->copy($permissionId);
            if (empty($permission->getErrors()) === true) {
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
	public function unpublish(PermissionServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $permissionService->unpublish($permissionId);
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を無効にしました。'), $permission->name));
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
	public function publish(PermissionServiceInterface $permissionService, $permissionId)
    {
        $permission = $permissionService->get($permissionId);
        $userGroupId = $permission->user_group_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $permissionService->publish($permissionId);
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を有効にしました。'), $permission->name));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * 一括公開
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_publish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, true);
            }
        }
        return true;
    }

    /**
     * 一括非公開
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_unpublish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, false);
            }
        }
        return true;
    }

    /**
     * ステータスを変更する
     *
     * @param int $id
     * @param boolean $status
     * @return boolean
     */
    protected function _changeStatus($id, $status)
    {
        $statusTexts = [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
        $data = $this->Permission->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
        $data['Permission']['status'] = $status;
        $this->Permission->set($data);

        if (!$this->Permission->save()) {
            return false;
        }

        $statusText = $statusTexts[$status];
        $this->Permission->saveDbLog(
            sprintf(
                'アクセス制限設定「%s」 を %s に設定しました。',
                $data['Permission']['name'],
                $statusText
            )
        );
        return true;
    }
}
