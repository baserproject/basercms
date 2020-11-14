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

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Utility\BcUtil;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Http\Response;
use Cake\Http\Exception\ForbiddenException;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
 * @property UsersTable $Users
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 */

class UsersController extends BcAdminAppController
{
	public $siteConfigs = [];

    /**
     * initialize
     * ログインページ認証除外
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
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
	 * ログインユーザーリスト
	 *
	 * 管理画面にログインすることができるユーザーの一覧を表示する
	 *
	 * - list view
     *  - User.id
	 *	- User.name
     *  - User.nickname
     *  - UserGroup.title
     *  - User.real_name_1 && User.real_name_2
     *  - User.created && User.modified
	 *
	 * - search input
	 *	- User.user_group_id
	 *
	 * - pagination
	 * - view num
	 */
    public function index(): void
    {
        $this->setViewConditions('User', ['default' => ['query' => ['num' => $this->siteConfigs['admin_list_num']]]]);
        $this->paginate = [
            'order' => ['Users.id'],
            'limit' => $this->request->getQuery('num'),
            'contain' => ['UserGroups']
        ];
        $query = $this->Users->find('all', $this->paginate);
        $query = $this->Users->createWhere($query, $this->request);
        $this->set([
            'users' => $this->paginate($query)
        ]);
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * 管理画面へログインする
	 * - link
     *	- パスワード再発行
     *
     * - viewVars
     *  - title
	 *
	 * - input
	 *	- User.name or User.email
     *	- User.password
     *  - remember login
     *  - submit
     *
     * @return void
     */
    public function login()
    {
        var_dump($this->Authentication->getLoginRedirect());
        exit;
        $this->set('title', '管理システムログイン');
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? Configure::read('BcPrefixAuth.Admin.loginRedirect');
            $user = $result->getData();

            // グループ情報等データセットを付与
            $user = $this->Users->getLoginData($user->id);
            $this->Authentication->setIdentity($user);

            $this->BcMessage->setInfo(__d('baser', 'ようこそ、' . $user->name . 'さん。'));
            $this->redirect($target);
            return;
        }
        if ($this->request->is('post') && !$result->isValid()) {
            $this->BcMessage->setError(__d('baser', 'Eメール、または、パスワードが間違っています。'));
        }
    }

    /**
     * ログイン状態のセッションを破棄する
     *
     * - redirect
     *   - login
     * @return void
     */
    public function logout()
    {
        $session = Router::getRequest()->getSession();
        $session->delete('AuthAgent');
        $this->Authentication->logout();
        $this->redirect(['action' => 'login']);
    }

    /**
     * ログインユーザー新規追加
     *
     * 管理画面にログインすることができるユーザーの各種情報を新規追加する
     *
     * - input
     *  - User.name
     *  - User.mail
     *  - User.password
     *  - User.real_name_1
     *  - User.real_name_2
     *  - User.nickname
     *  - UserGroup
     *  - submit
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $this->request = $this->request->withData('password', $this->request->getData('password_1'));
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'new']);
            if ($this->Users->save($user)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を追加しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $user = $this->Users->getNew();
        }

        /* 表示設定 */
        $userGroups = $this->Users->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title']);

        $selfUpdate = false;
        $editable = true;
        $deletable = false;
        $title = __d('baser', '新規ユーザー登録');
        // TODO: help
        // $this->help = 'users_form';
        $this->set(compact('user', 'userGroups', 'editable', 'selfUpdate', 'deletable', 'title'));
    }

    /**
     * ログインユーザー編集
     *
     * 管理画面にログインすることができるユーザーの各種情報を編集する
     *
     * - viewVars
     *  - User.no
     *  - User.name
     *  - User.mail
     *  - User.password
     *  - User.real_name_1
     *  - User.real_name_2
     *  - User.nickname
     *  - User.user_group_id
     *
     * - input
     *  - User.name
     *  - User.mail
     *  - User.password
     *  - User.real_name_1
     *  - User.real_name_2
     *  - User.nickname
     *  - User.user_group_id
     *  - submit
     *  - delete
     *
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $user = $this->Users->get($id, [
            'contain' => ['UserGroups'],
        ]);

        $selfUpdate = false;
        $updatable = true;

        // TODO：ログイン中のユーザーを取得
        // $user = $this->BcAuth->user();

        if ($this->request->is(['patch', 'post', 'put'])) {

            // TODO: ログイン中のユーザーが自分の場合の処理
            // if ($user->id == $this->request->getData('id')) {
            //     $selfUpdate = true;
            // }

            // TODO: 非特権ユーザは該当ユーザの編集権限があるか確認
            // if ($user['user_group_id'] !== Configure::read('BcApp.adminGroupId')) {
            //     if (!$this->UserGroup->Permission->check('/admin/users/edit/' . $this->request->getData('id'), $user['user_group_id'])) {
            //         $updatable = false;
            //     }
            // }

            $user = $this->Users->patchEntity($user, $this->request->getData());

            // 権限確認
            if (!$updatable) {
                $this->BcMessage->setError(__d('baser', '指定されたページへのアクセスは許可されていません。'));
            // TODO: 自身のアカウントは変更出来ないようにチェック
            // } elseif ($selfUpdate && $user['user_group_id'] != $this->request->getData('user_group_id')) {
                // $this->BcMessage->setError(__d('baser', '自分のアカウントのグループは変更できません。'));
            } else {
                if ($this->Users->save($user)) {
                    if ($selfUpdate) {
                        $this->logout();
                    }
                    $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                    return $this->redirect(['action' => 'edit', $user->id]);
                } else {
                    // TODO: よく使う項目のデータを再セット
                    // $user = $this->User->find('first', ['conditions' => ['User.id' => $id]]);
                    // unset($user['User']);
                    // $this->request->data = array_merge($user, $this->request->data);
                    $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
                }
            }
        } else {
            // ログイン中のユーザーが自分の場合の処理
            if ($user->id == $this->request->getData('id')) {
                 $selfUpdate = true;
            }
        }

        $userGroups = $this->Users->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title']);
        $editable = true;
        $deletable = true;

        // TODO
        // if (@$user['user_group_id'] != Configure::read('BcApp.adminGroupId') && Configure::read('debug') !== -1) {
        //     $editable = false;
        // } elseif ($selfUpdate && @$user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
        //     $deletable = false;
        // }

        $title = __d('baser', 'ユーザー情報編集');
        // TODO: help
        // $this->help = 'users_form';
        $this->set(compact('user', 'userGroups', 'editable', 'selfUpdate', 'deletable', 'title'));
    }

    /**
     * ログインユーザー削除
     *
     * 管理画面にログインすることができるユーザーを削除する
     *
     * @param string|null $id User id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        /* 除外処理 */
        $this->request->allowMethod(['post', 'delete']);

        // TODO： 最後のユーザーの場合は削除はできない
        // if ($this->User->field('user_group_id', ['User.id' => $id]) == Configure::read('BcApp.adminGroupId') &&
        //     $this->User->find('count', ['conditions' => ['User.user_group_id' => Configure::read('BcApp.adminGroupId')]]) == 1) {
        //     $this->BcMessage->setError(__d('baser', '最後の管理者ユーザーは削除する事はできません。'));
        //     $this->redirect(['action' => 'index']);
        // }

        // メッセージ用にデータを取得
        $user = $this->Users->get($id);

        /* 削除処理 */
        if ($this->Users->delete($user)) {
            $this->BcMessage->setSuccess(__d('baser', 'ユーザー: {0} を削除しました。', $user->name));
        } else {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * 代理ログイン
     *
     * 別のユーザにログインできる
     *
     * @param string|null $id User id.
     * @return Response Redirects
     * @throws RecordNotFoundException When record not found.
     */
    public function login_agent($id)
    {
        $session = Router::getRequest()->getSession();
        $user = BcUtil::loginUser();

        // 特権確認
        if (BcUtil::isSuperUser() === false) {
            throw new ForbiddenException();
        }

        // 既に代理ログイン済み
        if (BcUtil::isAgentUser()) {
            $this->BcMessage->setError(__d('baser', '既に代理ログイン中のため失敗しました。'));
            return $this->redirect(['action' => 'index']);
        }

        // 対象ユーザデータ取得
        $agentUser = $this->Users->getLoginData($id);
        $this->Authentication->setIdentity($agentUser);
        $session->write('AuthAgent.User', $user);
        $session->write('AuthAgent.referer', $this->referer());

        $target = $this->Authentication->getLoginRedirect() ?? Configure::read('BcPrefixAuth.Admin.loginRedirect');
        $this->redirect($target);
        return;
    }

    /**
     * 代理ログイン解除
     *
     * @return Response
     */
    public function back_agent()
    {
        $session = Router::getRequest()->getSession();
        $user = $session->read('AuthAgent.User');
        if (empty($user)) {
            $this->BcMessage->setError(__d('baser', '対象データが見つかりません。'));
            return $this->redirect(['action' => 'index']);
        }

        $this->Authentication->setIdentity($user);
        $target = $session->read('AuthAgent.referer') ?? Configure::read('BcPrefixAuth.Admin.loginRedirect');

        $session->delete('AuthAgent');
        $this->redirect($target);
    }
}
