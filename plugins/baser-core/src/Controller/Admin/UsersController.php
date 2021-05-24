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
use BaserCore\Model\Entity\User;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Service\UserManageServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Http\Response;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Cookie\Cookie;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
 * @property UsersTable $Users
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 * @property LoginStoresTable $loginStores
 */
class UsersController extends BcAdminAppController
{
    /**
     * サイト基本設定
     *
     * @var array
     */
    public $siteConfigs = [];

    /**
     * コンポーネント
     *
     * @var array
     */
     // TODO 未実装
     /* >>>
	public $components = ['BcReplacePrefix'];
    <<< */

    /**
     * initialize
     * ログインページ認証除外
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
        $this->loadModel('BaserCore.LoginStores');
    }

    /**
     * beforeFilter
     * @param EventInterface $event
     * @return Response|void|null
     * @checked
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // TODO 未実装、取り急ぎ動作させるためのコード
        // >>>
        $this->siteConfigs['admin_list_num'] = 30;
        // <<<
        // TODO 未実装
        /* >>>
        if (BC_INSTALLED) {
            if (isset($this->UserGroup)) {
                $this->set('usePermission', $this->UserGroup->checkOtherAdmins());
            }
        }
        $this->BcReplacePrefix->allow('login', 'logout', 'reset_password');
        <<< */
    }

    /**
     * 管理画面へログインする
     * - link
     *    - パスワード再発行
     *
     * - viewVars
     *  - title
     *  - savedEnable
     *
     * - input
     *    - User.name or User.email
     *    - User.password
     *    - User.saved
     *  - remember login
     *  - submit
     *
     * @return void
     * @checked
     * @unitTest
     */
    public function login()
    {
        // TODO 未実装
        /* >>>
        if ($this->BcAuth->loginAction != ('/' . $this->request->url)) {
            $this->notFound();
        }
        <<< */
        $pageTitle = __d('baser', 'ログイン');
        // TODO 未実装
        /* >>>
        $prefixAuth = Configure::read('BcAuthPrefix.' . $this->request->getParam('prefix'));
        if ($prefixAuth && isset($prefixAuth['loginTitle'])) {
            $pageTitle = $prefixAuth['loginTitle'];
        }
        <<< */
        $this->setTitle($pageTitle);
        $this->set('savedEnable', $this->request->is('ssl'));
        $result = $this->Authentication->getResult();
        if ($this->request->is('post')) {
            if ($result->isValid()) {
                $target = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
                $user = $result->getData();
                // グループ情報等データセットを付与
                $user = $this->Users->getLoginFormatData($user->id);
                $this->Authentication->setIdentity($user);
                $this->LoginStores->removeKey('Admin', $user->id);
                // 自動ログイン保存
                if ($this->request->is('ssl') && $this->request->getData('saved') == '1') {
                    $loginStore = $this->LoginStores->addKey('Admin', $user->id);
                    // クッキーを追加
                    $this->setCookieAutoLoginKey($loginStore->store_key);
                }
                $this->BcMessage->setInfo(__d('baser', 'ようこそ、' . $user->getDisplayName() . 'さん。'));
                $this->redirect($target);
                return;
            }
            if ($this->request->is('post') && !$result->isValid()) {
                $this->BcMessage->setError(__d('baser', 'Eメール、または、パスワードが間違っています。'));
            }
        } else {
            if ($this->Authentication->getResult()->isValid()) {
                $this->redirect(Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
            }
        }
    }

    /**
     * 代理ログイン
     *
     * 別のユーザにログインできる
     *
     * @param string|null $id User id.
     * @return Response|void Redirects
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login_agent($id): ?Response
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
        $agentUser = $this->Users->getLoginFormatData($id);
        $this->Authentication->setIdentity($agentUser);
        $session->write('AuthAgent.User', $user);
        $session->write('AuthAgent.referer', $this->referer());
        $target = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
        return $this->redirect($target);
    }

    /**
     * 代理ログイン解除
     *
     * @return Response
     * @unitTest
     * @checked
     */
    public function back_agent()
    {
        $session = Router::getRequest()->getSession();
        $user = $session->read('AuthAgent.User');
        // TODO 未精査
        /* >>>
        $configs = Configure::read('BcAuthPrefix');
        <<< */
        if (empty($user)) {
            $this->BcMessage->setError(__d('baser', '対象データが見つかりません。'));
            // TODO 未実装
            /* >>>
            if (!empty($this->request->getParam('prefix'))) {
                $authPrefix = $this->request->getParam('prefix');
            } else {
                $authPrefix = 'front';
            }
            if (!empty($configs[$authPrefix])) {
                $redirect = Router::url($configs[$authPrefix]['loginRedirect']);
            } else {
                $redirect = '/';
            }
            return $this->redirect($redirect);
            <<< */
            return $this->redirect(['action' => 'index']);
        }

        $this->Authentication->setIdentity($user);
        $this->BcMessage->setInfo(__d('baser', '元のユーザーに戻りました。'));

        // TODO 未実装
        /* >>>
        $authPrefix = explode(',', $user['UserGroup']['auth_prefix']);
        $authPrefix = $authPrefix[0];
        if (!empty($configs[$authPrefix])) {
            $redirect = Router::url($configs[$authPrefix]['loginRedirect']);
        } else {
            $redirect = '/';
        }
        <<< */
        $target = $session->read('AuthAgent.referer') ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));

        $session->delete('AuthAgent');
        return $this->redirect($target);
    }

    /**
     * ログイン状態のセッションを破棄する
     *
     * - redirect
     *   - login
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout()
    {
        // ログイン状態保存のデータ削除
        $user = $this->Authentication->getIdentity();
        /* @var User $user */
        $this->LoginStores->removeKey('Admin', $user->id);
        $this->response = $this->response->withExpiredCookie(new Cookie($this->LoginStores::KEY_NAME));
        $session = Router::getRequest()->getSession();
        $session->delete('AuthAgent');
        $this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

    /**
     * ログインユーザーリスト
     *
     * 管理画面にログインすることができるユーザーの一覧を表示する
     *
     * - list view
     *  - User.id
     *    - User.name
     *  - User.nickname
     *  - UserGroup.title
     *  - User.real_name_1 && User.real_name_2
     *  - User.created && User.modified
     *
     * - search input
     *    - User.user_group_id
     *
     * - pagination
     * - view num
     * @param UserManageServiceInterface $userManage
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserManageServiceInterface $userManage): void
    {
        $this->setViewConditions('User', ['default' => ['query' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Users.searchIndex
        $event = $this->getEventManager()->dispatch(new Event('Controller.Users.searchIndex', $this, [
            'request' => $this->request
        ]));
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }

        $this->set('users', $this->paginate($userManage->getIndex($this->request->getQueryParams())));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
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
     * @param UserManageServiceInterface $userManage
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserManageServiceInterface $userManage)
    {
        if ($this->request->is('post')) {
            if ($user = $userManage->create($this->request->getData())) {
                // EVENT Users.afterAdd
                $this->getEventManager()->dispatch(new Event('Controller.Users.afterAdd', $this, [
                    'user' => $user
                ]));
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を追加しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $user = $userManage->getNew();
        }
        $this->set('user', $user);
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
     * @param UserManageServiceInterface $userManage
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     */
    public function edit(UserManageServiceInterface $userManage, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $user = $userManage->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // TODO: 未実装 非特権ユーザは該当ユーザの編集権限があるか確認
            /* >>>
            if ($user['user_group_id'] !== Configure::read('BcApp.adminGroupId')) {
                $loginUser = BcUtil::loginUser();
                if (!$this->UserGroup->Permission->check('/admin/users/edit/' . $this->request->getData('id'), $loginUser['user_group_id'])) {
                    $updatable = false;
                }
            }
            <<< */
            if (!BcUtil::loginUser()->isAdmin() && $userManage->willChangeSelfGroup($this->getRequest()->getData())) {
                $this->BcMessage->setError(__d('baser', '自分のアカウントのグループは変更できません。'));
            } else {
                if ($user = $userManage->update($user, $this->request->getData())) {
                    $this->getEventManager()->dispatch(new Event('Controller.Users.afterEdit', $this, [
                        'user' => $user
                    ]));
                    if ($userManage->isSelfUpdate($user->id)) {
                        $this->Authentication->setIdentity($this->Users->getLoginFormatData($user->id));
                    }
                    $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                    return $this->redirect(['action' => 'edit', $user->id]);
                } else {
                    // TODO: よく使う項目のデータを再セット
                    /* >>>
                    $user = $this->User->find('first', ['conditions' => ['User.id' => $id]]);
                    unset($user['User']);
                    $this->request->data = array_merge($user, $this->request->data);
                    <<< */
                    $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
                }
            }
        }
        $this->set('user', $user);
    }

    /**
     * ログインユーザー削除
     *
     * 管理画面にログインすることができるユーザーを削除する
     *
     * @param UserManageServiceInterface $userManage
     * @param string|null $id User id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(UserManageServiceInterface $userManage, $id = null)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $user = $userManage->get($id);
        try {
            if ($userManage->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー: {0} を削除しました。', $user->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
