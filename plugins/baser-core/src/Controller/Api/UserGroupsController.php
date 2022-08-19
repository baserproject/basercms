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

use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Service\UserGroupsServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;


/**
 * Class UserGroupsController
 *
 * https://localhost/baser/api/baser-core/user_groups/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 * @property UserGroupsTable $UserGroups
 */
class UserGroupsController extends BcApiController
{
    /**
     * ユーザーグループ一覧取得
     * @param UserGroupsServiceInterface $UserGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserGroupsServiceInterface $UserGroups)
    {
        $this->set([
            'userGroups' => $this->paginate($UserGroups->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザーグループ取得
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UserGroupsServiceInterface $UserGroups, $id)
    {
        $this->set([
            'userGroups' => $UserGroups->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザーグループ登録
     * @param UserGroupsServiceInterface $UserGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserGroupsServiceInterface $UserGroups)
    {
        if ($this->request->is('post')) {
            try {
                $userGroups = $UserGroups->create($this->request->getData());
                $message = __d('baser', 'ユーザーグループ「{0}」を追加しました。', $userGroups->name);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $userGroups = $e->getEntity();
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups,
            'errors' => $userGroups->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'userGroups', 'errors']);
    }

    /**
     * ユーザーグループ編集
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserGroupsServiceInterface $UserGroups, $id)
    {
        $userGroups = $UserGroups->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $userGroups = $UserGroups->update($userGroups, $this->request->getData());
                $message = __d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroups->name);
            } catch (\Exception $e) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups,
            'errors' => $userGroups->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups', 'message', 'errors']);
    }

    /**
     * ユーザーグループ削除
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserGroupsServiceInterface $UserGroups, $id)
    {
        $userGroups = $UserGroups->get($id);
        if ($this->request->is(['post', 'delete'])) {
            try {
                if ($UserGroups->delete($id)) {
                    $message = __d('baser', 'ユーザー: {0} を削除しました。', $userGroups->name);
                }
            } catch (Exception $e) {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups', 'message']);
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
     * @param UserGroupsServiceInterface $UserGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(UserGroupsServiceInterface $userGroupService, $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $userGroup = null;
        $errors = null;
        try {
            $userGroup = $userGroupService->get($id);
            $rs = $this->UserGroups->copy($id);
            if ($rs) {
                $message = __d('baser', 'ユーザーグループ「{0}」をコピーしました。', $userGroup->name);
                $userGroup = $rs;
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。');
            }
        } catch (\Exception $e) {
            $errors = $e->getMessage();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }

        $this->set([
            'message' => $message,
            'userGroup' => $userGroup,
            'errors' => $errors,
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'userGroup', 'errors']);
    }
}
