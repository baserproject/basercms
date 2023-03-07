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
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class PermissionsController
 * @uses PermissionsController
 */
class PermissionsController extends BcApiController
{
    /**
     * [API] 単一アクセスルール取得
     * @param PermissionsServiceInterface $service
     * @param int $id
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(PermissionsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $permission = $message = null;
        try {
            $permission = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'permission' => $permission,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission', 'message']);
    }

    /**
     * [API] アクセスルールの一覧
     * @param PermissionsServiceInterface $service
     * @param int $userGroupId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PermissionsServiceInterface $service, int $userGroupId)
    {
        $this->request->allowMethod(['get']);

        $this->request = $this->request->withQueryParams(['user_group_id' => $userGroupId]);
        $this->set('permissions', $this->paginate($service->getIndex($this->request->getQueryParams())));
        $this->viewBuilder()->setOption('serialize', ['permissions']);
    }

    /**
     * 登録処理
     *
     * @param PermissionsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(PermissionsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permission = $errors = null;
        try {
            $permission = $service->create($this->request->getData());
            $message = __d('baser_core', '新規アクセスルール「{0}」を追加しました。', $permission->name);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

    /**
     * [API] 削除処理
     *
     * @param PermissionsServiceInterface $service
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PermissionsServiceInterface $service, int $permissionId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $permission = null;
        try {
            $permission = $service->get($permissionId);
            $service->delete($permissionId);
            $message = __d('baser_core', 'アクセスルール「{0}」を削除しました。', $permission->name);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'permission' => $permission,
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission', 'message']);
    }

    /**
     * [API] アクセスルールコピー
     *
     * @param PermissionsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(PermissionsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $permission = $errors = null;
        try {
            $permission = $service->copy($id);
            if ($permission) {
                $message = __d('baser_core', 'アクセスルール「{0}」をコピーしました。', $permission->name);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

    /**
     * [API] 編集処理
     *
     * @param PermissionsServiceInterface $service
     * @param int $permissionId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(PermissionsServiceInterface $service, int $permissionId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $permission = $errors = null;
        try {
            $permission = $service->update($service->get($permissionId), $this->request->getData());
            $message = __d('baser_core', 'アクセスルール「{0}」を更新しました。', $permission->name);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $errors,
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
            'publish' => __d('baser_core', '有効化'),
            'unpublish' => __d('baser_core', '無効化'),
            'delete' => __d('baser_core', '削除'),
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $errors = null;
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getNamesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'アクセスルール 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * 並び替えを更新する
     *
     * @param PermissionsService $service
     * @param int $userGroupId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update_sort(PermissionsServiceInterface $service, int $userGroupId)
    {
        $this->request->allowMethod(['post']);
        $conditions = [
            'user_group_id' => $userGroupId,
        ];
        $permission = $service->get($this->request->getData('id'));
        if (!$service->changeSort($this->request->getData('id'), $this->request->getData('offset'), $conditions)) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '一度リロードしてから再実行してみてください。');
        } else {
            $message = sprintf(__d('baser_core', 'アクセスルール「%s」の並び替えを更新しました。'), $permission->name);
        }
        $this->set([
            'message' => $message,
            'permission' => $permission
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

}
