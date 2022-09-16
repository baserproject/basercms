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

use BaserCore\Error\BcException;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PluginsServiceInterface;
use Cake\Utility\Inflector;

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
     *
     * @param PermissionsServiceInterface $permissionService
     *
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
     * [API] 削除処理
     *
     * @param PermissionsServiceInterface $permissionService
     * @param $permissionId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $error = null;
        $permission = null;
        try {
            $permission = $permissionService->get($permissionId);
            $permissionName = $permission->name;
            $permissionService->delete($permissionId);
            $message = __d('baser', 'アクセス制限設定「{0}」を削除しました。', $permissionName);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $error);
        }

        $this->set([
            'message' => $message,
            'permission' => $permission,
            'error' => $error,
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission', 'message', 'error']);
    }

    /**
     * [API] アクセス制限設定コピー
     *
     * @param PermissionsServiceInterface $permissionService
     * @param $id
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
        } else {
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
                $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $errors);
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

    /**
     * 一括処理
     *
     * @param PermissionsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(PermissionsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'publish' => '有効化',
            'unpublish' => '無効化',
            'delete' => '削除',
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getNamesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'アクセス制限設定 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * 並び替えを更新する [AJAX]
     *
     * @access    public
     * @param $userGroupId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update_sort(PermissionsServiceInterface $service, $userGroupId)
    {
        $this->request->allowMethod(['post']);
        $conditions = [
            'user_group_id' => $userGroupId,
        ];
        $permission = $service->get($this->request->getData('id'));
        if (!$service->changeSort($this->request->getData('id'), $this->request->getData('offset'), $conditions)) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '一度リロードしてから再実行してみてください。');
        } else {
            $message = sprintf(__d('baser', 'アクセス制限設定「%s」の並び替えを更新しました。'), $permission->name);
        }
        $this->set([
            'message' => $message,
            'permission' => $permission
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

}
