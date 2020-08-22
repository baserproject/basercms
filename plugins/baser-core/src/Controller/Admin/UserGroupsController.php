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
        $this->siteConfigs['admin_list_num'] = 20;
        // $this->request = $this->request->withParam('pass', ['num' => 20]);
        // <<<

    }

    /**
     * ログインユーザーグループリスト
     *
     * 管理画面にログインすることができるユーザーグループの一覧を表示する
     *
     * - list view
     *  - UserGroup.id
     *	- UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.created && UserGroup.modified
     *
     * - search input
     *	- UserGroup.name
     *
     * - pagination
     * - view num
     *
     * @return Response|null|void Renders view
     */
    public function index()
    {
        $this->request = $this->request->withParam('pass', ['num' => $this->siteConfigs['admin_list_num']]);
        $default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
        $this->setViewConditions('UserGroup', ['default' => $default]);
        $userGroups = $this->paginate(
            $this->UserGroups->find('all')
                ->limit($this->request->getParam('pass')['num'])
        );
        $this->set([
            'userGroups' => $userGroups,
            '_serialize' => ['userGroups']
        ]);

        // TODO: help
        // $this->help = 'user_groups_index';
        $this->set('title', __d('baser', 'ユーザーグループ一覧'));
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
            $userGroup = $this->UserGroups->patchEntity($userGroup, $this->request->getData());
            if ($this->UserGroups->save($userGroup)) {
                $this->BcMessage->setSuccess(__d('baser', '新規ユーザーグループ「{0}」を追加しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }

        $title = __d('baser', '新規ユーザーグループ登録');
        // TODO: help
        // $this->help = 'user_groups_form';
        $this->set(compact('userGroup', 'title'));
        $this->render('form');
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
            'contain' => ['users'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userGroup = $this->UserGroups->patchEntity($userGroup, $this->request->getData());
            if ($this->UserGroups->save($userGroup)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        }

        $title = __d('baser', 'ユーザーグループ編集');
        // TODO: help
        // $this->help = 'user_groups_form';
        $this->set(compact('userGroup', 'title'));
        $this->render('form');
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
}
