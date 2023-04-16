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

namespace BaserCore\Controller\Api\Admin;

use BaserCore\Service\PermissionGroupsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PermissionGroupsController
 */
class PermissionGroupsController extends BcAdminApiController
{
    /**
     * [API] 単一アクセスルールグループ取得
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(PermissionGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $permissionGroup = $message = null;
        try {
            $permissionGroup = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'permissionGroup' => $permissionGroup,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['permissionGroup', 'message']);
    }

    /**
     * [API] アクセスルールグループの一覧
     * @param PermissionGroupsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PermissionGroupsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (empty($queryParams['user_group_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに user_group_id を指定してください。'));
        }
        $this->set([
            'permissionGroups' => $this->paginate(
                $service->getIndex($queryParams['user_group_id'], $this->getRequest()->getQueryParams())
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['permissionGroups']);
    }

    /**
     * [API] アクセスルールグループのリスト
     * @param PermissionGroupsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(PermissionGroupsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'permissionGroups' => $service->getList($this->getRequest()->getQueryParams())
        ]);
        $this->viewBuilder()->setOption('serialize', ['permissionGroups']);
    }

    /**
     * 登録処理
     *
     * @param PermissionGroupsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(PermissionGroupsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        $permissionGroup = $errors = null;
        try {
            $permissionGroup = $service->create($this->request->getData());
            $message = __d('baser_core', 'ルールグループ「{0}」を登録しました。', $permissionGroup->name);
            $this->BcMessage->setSuccess($message, true, false);
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
            'permissionGroup' => $permissionGroup,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'permissionGroup', 'errors']);
    }

    /**
     * [API] 削除処理
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PermissionGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permissionGroup = null;
        try {
            $permissionGroup = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'ルールグループ「{0}」を削除しました。', $permissionGroup->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'permissionGroup' => $permissionGroup
        ]);
        $this->viewBuilder()->setOption('serialize', ['permissionGroup', 'message']);
    }

    /**
     * [API] 編集処理
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(PermissionGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $permissionGroup = $errors = null;
        try {
            $permissionGroup = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'ルールグループ「{0}」を更新しました。', $permissionGroup->name);
            $this->BcMessage->setSuccess($message, true, false);
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
            'permissionGroup' => $permissionGroup,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['permissionGroup', 'message', 'errors']);
    }


    /**
     * [API] ユーザーグループを指定してアクセスルールを再構築する
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $userGroupId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rebuild_by_user_group(PermissionGroupsServiceInterface $service, int $userGroupId)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            if ($service->rebuildByUserGroup($userGroupId)) {
                $message = __d('baser_core', 'アクセスルールの再構築に成功しました。');
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'アクセスルールの再構築に失敗しました。');
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
