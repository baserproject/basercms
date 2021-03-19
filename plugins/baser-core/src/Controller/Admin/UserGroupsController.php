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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\UserGroupsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsController
 * @package BaserCore\Controller\Admin
 * @property UserGroupsTable $UserGroups
 * @property BcMessageComponent $BcMessage
 * @method UserGroup[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserGroupsController extends BcAdminAppController
{
    public $siteConfigs = [];


    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        // $this->Authentication->allowUnauthenticated([]);
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        // TODO 取り急ぎ動作させるためのコード
        // >>>
        $this->siteConfigs['admin_list_num'] = 30;
        // $this->request = $this->request->withParam('pass', ['num' => 30]);
        // <<<

    }

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
     *
     * @return Response|null|void Renders view
     */
    public function index()
    {
        $this->setViewConditions('UserGroup', ['default' => ['query' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);
        $this->paginate = [
            'limit' => $this->request->getQuery('num'),
        ];
        $query = $this->UserGroups->find('all', $this->paginate);
        $this->set([
            'userGroups' => $this->paginate($query),
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
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userGroup = $this->UserGroups->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (empty($data['auth_prefix'])) {
                $data['auth_prefix'] = 'Admin';
            } else {
                $data['auth_prefix'] = implode(',', $data['auth_prefix']);
            }
            $userGroup = $this->UserGroups->patchEntity($userGroup, $data);
            if ($this->UserGroups->save($userGroup)) {
                $this->BcMessage->setSuccess(__d('baser', '新規ユーザーグループ「{0}」を追加しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }

        $this->setTitle(__d('baser', '新規ユーザーグループ登録'));
        $this->setHelp('user_groups_form');
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
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userGroup = $this->UserGroups->get($id, [
            'contain' => ['Users'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();
            if (empty($data['auth_prefix'])) {
                $data['auth_prefix'] = 'Admin';
            } else {
                $data['auth_prefix'] = implode(',', $data['auth_prefix']);
            }
            $userGroup = $this->UserGroups->patchEntity($userGroup, $data);

            if ($this->UserGroups->save($userGroup)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }

        $this->setTitle(__d('baser', 'ユーザーグループ編集'));
        $this->setHelp('user_groups_form');
        $this->set(compact('userGroup'));
    }

    /**
     * ユーザーグループ削除
     *
     * ユーザーグループを削除する
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userGroup = $this->UserGroups->get($id);
        if ($this->UserGroups->delete($userGroup)) {
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
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @throws CopyFailedException When copy failed.
     */
    public function copy($id = null)
    {
        $this->request->allowMethod(['post']);
        if (!$id || !is_numeric($id)) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $userGroup = $this->UserGroups->get($id);
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
