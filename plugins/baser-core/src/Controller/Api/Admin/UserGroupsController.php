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

use BaserCore\Service\UserGroupsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsController
 */
class UserGroupsController extends BcAdminApiController
{
    /**
     * ユーザーグループ一覧取得
     * @param UserGroupsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserGroupsServiceInterface $service)
    {
        $this->set([
            'userGroups' => $this->paginate($service->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザーグループ取得
     * @param UserGroupsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UserGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $userGroup = $message = null;
        try {
            $userGroup = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'userGroup' => $userGroup,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroup', 'message']);
    }

    /**
     * ユーザーグループ登録
     * @param UserGroupsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserGroupsServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $userGroup = $errors = null;
        try {
            $userGroup = $service->create($this->request->getData());
            $message = __d('baser_core', 'ユーザーグループ「{0}」を追加しました。', $userGroup->name);
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
            'userGroup' => $userGroup,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'userGroup', 'errors']);
    }

    /**
     * ユーザーグループ編集
     * @param UserGroupsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $userGroup = $errors = null;
        try {
            $userGroup = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name);
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
            'message' => $message,
            'userGroup' => $userGroup,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroup', 'message', 'errors']);
    }

    /**
     * ユーザーグループ削除
     * @param UserGroupsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userGroup = null;
        try {
            $userGroup = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'ユーザー: {0} を削除しました。', $userGroup->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'userGroup' => $userGroup
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroup', 'message']);
    }

    /**
     * リスト出力
     * @param UserGroupsServiceInterface $userGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(UserGroupsServiceInterface $userGroups)
    {
        $this->set([
            'userGroups' => $userGroups->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザーグループコピー
     * @param UserGroupsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(UserGroupsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $userGroup = null;
        try {
            $userGroup = $service->get($id);
            $rs = $this->UserGroups->copy($id);
            if ($rs) {
                $message = __d('baser_core', 'ユーザーグループ「{0}」をコピーしました。', $userGroup->name);
                $userGroup = $rs;
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'userGroup' => $userGroup
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'userGroup']);
    }
}
