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

namespace BaserCore\Controller\Api;

use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class PermissionsController
 * @uses PermissionsController
 */
class PermissionsController extends BcApiController
{
    /**
     * [API] 単一アクセス制限設定取得
     * @param PermissionsServiceInterface $permissionsService
     * @param $id
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(PermissionsServiceInterface $permissionsService, $id)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'permission' => $permissionsService->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission']);
    }

    /**
     * [API] アクセス制限設定の一覧
     * @param PermissionsServiceInterface $permissionService
     * @param $userGroupId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PermissionsServiceInterface $permissionService, $userGroupId)
    {
        $this->request->allowMethod(['get']);

        $this->request = $this->request->withQueryParams(['user_group_id' => $userGroupId]);
        $this->set('permissions', $permissionService->getIndex($this->request->getQueryParams()));
        $this->viewBuilder()->setOption('serialize', ['permissions']);
    }

	/**
	 * 登録処理
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function add(PermissionsServiceInterface $permissionService)
	{
        $this->request->allowMethod(['post', 'delete']);
        try {
            $permission = $permissionService->create($this->request->getData());
            $message = __d('baser', '新規アクセス制限設定「{0}」を追加しました。', $permission->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $permission = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $permission->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

    /**
     * [API] アクセス制限設定コピー
     * @param PermissionsServiceInterface $permissionService
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(PermissionsServiceInterface $permissionService, $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $permission = null;
        $errors = null;

        if (!$id || !is_numeric($id)) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理に失敗しました。');
        }else{
            try {
                $permission = $permissionService->copy($id);
                if ($permission) {
                    $message = __d('baser', 'アクセス制限設定「{0}」をコピーしました。', $permission->name);
                } else {
                    $this->setResponse($this->response->withStatus(400));
                    $message = __d('baser', 'データベース処理中にエラーが発生しました。');
                }
            } catch (\Exception $e) {
                $errors = $e->getMessage();
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。'.$errors);
            }
        }

        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $errors,
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

    /**
     * [API] 編集処理
     *
     * @param PermissionsServiceInterface $permissionService
     * @param $permissionId
     *
     * @checked
     * @noTodo
     * @unitTest
     */

    public function edit(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $permission = $permissionService->update($permissionService->get($permissionId), $this->request->getData());
            $message = __d('baser', 'アクセス制限設定「{0}」を更新しました。', $permission->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $permission = $e->getEntity();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $permission->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission', 'message', 'errors']);
    }

}
