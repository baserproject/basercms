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

use BaserCore\Utility\BcSiteConfig;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Entity\UserGroup;
use Cake\Datasource\ResultSetInterface;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use Cake\Datasource\Exception\RecordNotFoundException;
use BaserCore\Model\Table\Exception\CopyFailedException;

/**
 * Class UserGroupsController
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
     * @param UserGroupsServiceInterface $service
     * @return Response|null|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserGroupsServiceInterface $service)
    {
        $this->setViewConditions('UserGroup', ['default' => ['query' => [
            'limit' => BcSiteConfig::get('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        try {
            $entities = $this->paginate($service->getIndex($this->getRequest()->getQueryParams()));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }
        $this->set([
            'userGroups' => $entities,
            '_serialize' => ['userGroups']
        ]);
    }

    /**
     * ユーザーグループ新規追加
     *
     * ユーザーグループの各種情報を新規追加する
     *
     * @param UserGroupsServiceInterface $service
     * @return Response|null|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserGroupsServiceInterface $service)
    {
        if ($this->request->is('post')) {
            try {
                $userGroup = $service->create($this->request->getData());
                $this->BcMessage->setSuccess(__d('baser', '新規ユーザーグループ「{0}」を追加しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $userGroup = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set('userGroup', $userGroup ?? $service->getNew());
    }

    /**
     * ユーザーグループ編集
     *
     * ユーザーグループの各種情報を編集する
     *
     * @param UserGroupsServiceInterface $service
     * @param string|null $id
     * @return Response|null|void
     * @throws RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserGroupsServiceInterface $service, UsersServiceInterface $usersService, $id)
    {
        $userGroup = $service->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $userGroup = $service->update($userGroup, $this->request->getData());
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name));
                $usersService->reLogin($this->request, $this->response);
                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set('userGroup', $userGroup);
    }

    /**
     * ユーザーグループ削除
     *
     * ユーザーグループを削除する
     * @param UserGroupsServiceInterface $service
     * @param string|null $id
     * @return Response|null|void
     * @throws RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserGroupsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userGroup = $service->get($id);
        if ($service->delete($id)) {
            $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を削除しました。', $userGroup->name));
        } else {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * ユーザーグループコピー
     *
     * ユーザーグループをコピーする
     * @param UserGroupsServiceInterface $service
     * @param string|null $id
     * @return Response|null|void
     * @throws RecordNotFoundException
     * @throws CopyFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(UserGroupsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post']);
        $userGroup = $service->get($id);
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

}
