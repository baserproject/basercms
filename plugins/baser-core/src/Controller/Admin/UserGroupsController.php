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

use Cake\Http\Response;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Entity\UserGroup;
use Cake\Datasource\ResultSetInterface;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Service\UserGroupServiceInterface;
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use Cake\Datasource\Exception\RecordNotFoundException;
use BaserCore\Model\Table\Exception\CopyFailedException;

/**
 * Class UserGroupsController
 * @package BaserCore\Controller\Admin
 * @property UserGroupsTable $UserGroups
 * @property BcMessageComponent $BcMessage
 * @method UserGroup[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserGroupsController extends BcAdminAppController
{

    /**
     * ログインユーザーグループリスト
     *
     * 管理画面にログインすることができるユーザーグループの一覧を表示する
     *
     * - list view
     *  - UserGroup.id
     *    - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.created && UserGroup.modified
     *
     * - search input
     *    - UserGroup.name
     *
     * - pagination
     * - view num
     * @param UserGroupServiceInterface $userGroupService
     * @return Response|null|void Renders view
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserGroupServiceInterface $userGroupService, SiteConfigServiceInterface $siteConfigService): void
    {
        $this->setViewConditions('UserGroup', ['default' => ['query' => [
            'num' => $siteConfigService->getValue('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);
        $this->paginate = [
            'limit' => $this->request->getQuery('num'),
        ];

        $this->set([
            'userGroups' => $this->paginate($userGroupService->getIndex($this->paginate)),
            '_serialize' => ['userGroups']
        ]);

        $this->setHelp('user_groups_index');
        $this->setTitle(__d('baser', 'ユーザーグループ一覧'));
    }

    /**
     * ユーザーグループ新規追加
     *
     * ユーザーグループの各種情報を新規追加する
     *
     * - input
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *  - submit
     * @param UserGroupServiceInterface $userGroupService
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserGroupServiceInterface $userGroupService)
    {
        $this->setTitle(__d('baser', '新規ユーザーグループ登録'));
        $this->setHelp('user_groups_form');

        if ($this->request->is('post')) {
            $userGroup = $userGroupService->create($this->request->getData());
            if (!$userGroup->getErrors()) {
                $this->BcMessage->setSuccess(__d('baser', '新規ユーザーグループ「{0}」を追加しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        } else {
            $userGroup = $userGroupService->getNew();
        }
        $this->set(compact('userGroup'));
    }

    /**
     * ユーザーグループ編集
     *
     * ユーザーグループの各種情報を編集する
     *
     * - viewVars
     *  - UserGroup.id
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *
     * - input
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *  - submit
     * @param UserGroupServiceInterface $userGroupService
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserGroupServiceInterface $userGroupService, UserServiceInterface $userService, $id = null)
    {

        if ($id) {
            $userGroup = $userGroupService->get($id);
            $this->setTitle(__d('baser', 'ユーザーグループ編集'));
            $this->setHelp('user_groups_form');
        } else {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            return $this->redirect(['action' => 'index']);

        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $userGroup = $userGroupService->update($userGroup, $this->request->getData());
            if (!$userGroup->getErrors()) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name));
                $userService->reLogin($this->request, $this->response);
                return $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
                return;
            }
        } else {
            $this->set(compact('userGroup'));
        }
    }

    /**
     * ユーザーグループ削除
     *
     * ユーザーグループを削除する
     * @param UserGroupServiceInterface $userGroupService
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserGroupServiceInterface $userGroupService, $id = null)
    {
        if ($id) {
            $this->request->allowMethod(['post', 'delete']);
            $userGroup = $userGroupService->get($id);

            if ($userGroupService->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を削除しました。', $userGroup->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
            return $this->redirect(['action' => 'index']);
        } else {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * ユーザーグループコピー
     *
     * ユーザーグループをコピーする
     * @param UserGroupServiceInterface $userGroupService
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @throws CopyFailedException When copy failed.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(UserGroupServiceInterface $userGroupService, $id = null)
    {
        $this->request->allowMethod(['post']);
        if (!$id || !is_numeric($id)) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $userGroup = $userGroupService->get($id);
        try {
            if ($this->UserGroups->copy($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」をコピーしました。', $userGroup->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Exception $e) {
            $message = [$e->getMessage()];
            $errors = $e->getErrors();
            if (!empty($errors)) {
                foreach($errors as $error) $message[] = __d('baser', current($error));
            }
            $this->BcMessage->setError(implode("\n", $message), false);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * ユーザーグループのよく使う項目の初期値を登録する
     * ユーザー編集画面よりAjaxで呼び出される
     *
     * @param $id
     * @return void
     * @throws Exception
     */
    public function set_default_favorites($id)
    {
        if (!$this->request->is(['post', 'put'])) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $defaultFavorites = null;
        if ($this->request->getData()) {
            $defaultFavorites = BcUtil::serialize($this->request->getData());
        }
        $this->UserGroup->id = $id;
        $this->UserGroup->recursive = -1;
        $data = $this->UserGroup->read();
        $data['UserGroup']['default_favorites'] = $defaultFavorites;
        $this->UserGroup->set($data);
        if ($this->UserGroup->save()) {
            echo true;
        }
        exit();
    }

}
