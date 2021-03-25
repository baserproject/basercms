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
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Http\Response;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
 * @property UsersTable $Users
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
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
     * TODO BcReplacePrefix は未精査
     */
//	public $components = ['BcReplacePrefix', 'BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail'];

    /**
     * UsersController constructor.
     *
     * @param \Cake\Http\ServerRequest|null $request Request object for this controller. Can be null for testing,
     *   but expect that features that use the request parameters will not work.
     * @param \Cake\Http\Response|null $response Response object for this controller.
     * @param string|null $name Override the name useful in testing when using mocks.
     * @param \Cake\Event\EventManagerInterface|null $eventManager The event manager. Defaults to a new instance.
     * @param \Cake\Controller\ComponentRegistry|null $components The component registry. Defaults to a new instance.
     * @checked
     */
    public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?string $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry $components = null
    )
    {
        parent::__construct($request, $response, $name, $eventManager, $components);
        $this->crumbs = [
            ['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
            ['name' => __d('baser', 'ユーザー管理'), 'url' => ['controller' => 'users', 'action' => 'index']]
        ];
    }

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
     * beforeFilter
     * @param EventInterface $event
     * @return Response|void|null
     * @checked
     */
    public function beforeFilter(EventInterface $event)
    {
        // TODO 未実装、取り急ぎ動作させるためのコード
        // >>>
        $this->siteConfigs['admin_list_num'] = 30;
        parent::beforeFilter($event);
        return;
        // <<<

        if (BC_INSTALLED) {
            /* 認証設定 */
            // parent::beforeFilterの前に記述する必要あり
            $this->BcAuth->allow(
                'admin_login', 'admin_logout', 'admin_login_exec', 'admin_reset_password'
            );
            if (isset($this->UserGroup)) {
                $this->set('usePermission', $this->UserGroup->checkOtherAdmins());
            }
        }

        parent::beforeFilter($event);
        $this->BcReplacePrefix->allow('login', 'logout', 'login_exec', 'reset_password');
    }

    /**
     * ログイン処理を行う
     * ・リダイレクトは行わない
     * ・requestActionから呼び出す
     *
     * @return boolean
     */
    public function login_exec()
    {
        if (!$this->request->getData()) {
            return false;
        }
        if ($this->BcAuth->login()) {
            return true;
        }
        return false;
    }

    /**
     * 管理画面へログインする
     * - link
     *    - パスワード再発行
     *
     * - viewVars
     *  - title
     *
     * - input
     *    - User.name or User.email
     *    - User.password
     *  - remember login
     *  - submit
     *
     * @return void
     * @checked
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
        $prefixAuth = Configure::read('BcAuthPrefix.' . $this->request->params['prefix']);
        if ($prefixAuth && isset($prefixAuth['loginTitle'])) {
            $pageTitle = $prefixAuth['loginTitle'];
        }
        <<< */
        $this->setTitle($pageTitle);
        $result = $this->Authentication->getResult();
        if($this->request->is('post')) {
            if ($result->isValid()) {
                $target = $this->Authentication->getLoginRedirect() ?? Configure::read('BcPrefixAuth.Admin.loginRedirect');
                $user = $result->getData();

                // グループ情報等データセットを付与
                $user = $this->Users->getLoginFormatData($user->id);
                $this->Authentication->setIdentity($user);
                // TODO 未実装
                /* >>>
                App::uses('BcBaserHelper', 'View/Helper');
                $BcBaser = new BcBaserHelper(new View());
                $this->BcMessage->setInfo(sprintf(__d('baser', 'ようこそ、%s さん。'), $BcBaser->getUserName($user)));
                <<< */
                $this->BcMessage->setInfo(__d('baser', 'ようこそ、' . $user->name . 'さん。'));
                $this->redirect($target);
                return;
            }
            if ($this->request->is('post') && !$result->isValid()) {
                $this->BcMessage->setError(__d('baser', 'Eメール、または、パスワードが間違っています。'));
            }
        } else {
            // TODO 未実装
            /* >>>
            $user = $this->BcAuth->user();
            if ($user && $this->isAuthorized($user)) {
                $this->redirect($this->BcAuth->redirectUrl());
            }
            <<< */
        }
    }

    /**
     * 代理ログイン
     *
     * 別のユーザにログインできる
     *
     * @param string|null $id User id.
     * @return Response Redirects
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
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
        $agentUser = $this->Users->getLoginFormatData($id);
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
        // TODO 未精査
        /* >>>
        $configs = Configure::read('BcAuthPrefix');
        <<< */
        if (empty($user)) {
            $this->BcMessage->setError(__d('baser', '対象データが見つかりません。'));
            // TODO 未実装
            /* >>>
            if (!empty($this->request->params['prefix'])) {
                $authPrefix = $this->request->params['prefix'];
            } else {
                $authPrefix = 'front';
            }
            if (!empty($configs[$authPrefix])) {
                $redirect = $configs[$authPrefix]['loginRedirect'];
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
            $redirect = $configs[$authPrefix]['loginRedirect'];
        } else {
            $redirect = '/';
        }
        <<< */
        $target = $session->read('AuthAgent.referer') ?? Configure::read('BcPrefixAuth.Admin.loginRedirect');

        $session->delete('AuthAgent');
        return $this->redirect($target);
    }

    /**
     * 認証クッキーをセットする
     *
     * @param array $data
     * @return void
     */
    public function setAuthCookie($data)
    {
        $userModel = $this->BcAuth->authenticate['Form']['userModel'];
        $cookie = [];
        foreach($data[$userModel] as $key => $val) {
            // savedは除外
            if ($key !== "saved") {
                $cookie[$key] = $val;
            }
        }
        $this->Cookie->httpOnly = true;
        $this->Cookie->write(Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey)), $cookie, true, '+2 weeks');    // 3つめの'true'で暗号化
    }

    /**
     * ログイン状態のセッションを破棄する
     *
     * - redirect
     *   - login
     * @return void
     * @checked
     * @noTodo
     */
    public function logout()
    {
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
     * @checked
     */
    public function index(): void
    {
        $this->setTitle(__d('baser', 'ユーザー一覧'));
        $this->setSearch('users_index');
        $this->setHelp('users_index');

        $this->setViewConditions('User', ['default' => ['query' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);
        $this->paginate = [
            'limit' => $this->request->getQuery('num'),
            'contain' => ['UserGroups']
        ];

        // EVENT Users.searchIndex
        // TODO 未実装 $this->paginate を書き換える処理にする
        /* >>>
        $event = $this->getEventManager()->dispatch(new CakeEvent('Controller.Users.searchIndex', $this, [
            'options' => $options
        ]));
        if ($event !== false) {
            $options = ($event->result === null || $event->result === true)? $event->data['options'] : $event->result;
        }
        <<< */

        $query = $this->Users->find('all', $this->paginate);
        $query = $this->Users->createWhere($query, $this->request);
        $this->set([
            'users' => $this->paginate($query)
        ]);
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
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $this->request = $this->request->withData('password', $this->request->getData('password_1'));
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'new']);
            if ($this->Users->save($user)) {
                // TODO 未実装
                /* >>>
                $this->getEventManager()->dispatch(new CakeEvent('Controller.Users.afterAdd', $this, [
                    'user' => $this->request->data
                ]));
                <<< */
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
        $this->setTitle(__d('baser', '新規ユーザー登録'));
        $this->setHelp('users_form');
        $this->set(compact('user', 'userGroups', 'editable', 'selfUpdate', 'deletable'));
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

        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }

        $user = $this->Users->get($id, [
            'contain' => ['UserGroups'],
        ]);

        $selfUpdate = false;
        $updatable = true;

        // TODO：ログイン中のユーザーを取得
        // $user = $this->BcAuth->user();

        if ($this->request->is(['patch', 'post', 'put'])) {

            // TODO: 未実装 ログイン中のユーザーが自分の場合の処理
            // if ($user->id == $this->request->getData('id')) {
            //     $selfUpdate = true;
            // }

            // パスワードがない場合は更新しない
            // TODO 未実装
            /* >>>
            if ($this->request->data['User']['password_1'] || $this->request->data['User']['password_2']) {
                $this->request->data['User']['password'] = $this->request->data['User']['password_1'];
            }
            <<< */

            // TODO: 未実装 非特権ユーザは該当ユーザの編集権限があるか確認
            // if ($user['user_group_id'] !== Configure::read('BcApp.adminGroupId')) {
            //     if (!$this->UserGroup->Permission->check('/admin/users/edit/' . $this->request->getData('id'), $user['user_group_id'])) {
            //         $updatable = false;
            //     }
            // }

            $user = $this->Users->patchEntity($user, $this->request->getData());

            // 権限確認
            if (!$updatable) {
                $this->BcMessage->setError(__d('baser', '指定されたページへのアクセスは許可されていません。'));
            // TODO: 未実装 自身のアカウントは変更出来ないようにチェック
            /* >>>
            } elseif ($selfUpdate && $user['user_group_id'] != $this->request->getData('user_group_id')) {
                $this->BcMessage->setError(__d('baser', '自分のアカウントのグループは変更できません。'));
            <<< */
            } else {
                if ($this->Users->save($user)) {
                    // TODO 未実装
                    /* >>>
                    $this->getEventManager()->dispatch(new CakeEvent('Controller.Users.afterEdit', $this, [
                        'user' => $this->request->data
                    ]));
                    */

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

        $this->setTitle(__d('baser', 'ユーザー情報編集'));
        $this->setHelp('users_form');
        $this->set(compact('user', 'userGroups', 'editable', 'selfUpdate', 'deletable'));
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
        // TODO 未実装
        /* >>>
        $this->_checkSubmitToken();
        <<< */

        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }

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
     * ログインパスワードをリセットする
     * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
     *
     * @return void
     */
    public function reset_password()
    {
        if ((empty($this->request->getParam('prefix')) && !Configure::read('BcAuthPrefix.front'))) {
            $this->notFound();
        }
        if ($this->BcAuth->user()) {
            $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
        }
        $this->pageTitle = __d('baser', 'パスワードのリセット');
        $userModel = $this->BcAuth->authenticate['Form']['userModel'];
        if (strpos($userModel, '.') !== false) {
            [, $userModel] = explode('.', $userModel);
        }
        if (!$this->request->getData()) {
            return;
        }

        $email = $this->request->getData("{$userModel}.email")? $this->request->getData("{$userModel}.email") : '';

        if (mb_strlen($email) === 0) {
            $this->BcMessage->setError('メールアドレスを入力してください。');
            return;
        }
        $user = $this->{$userModel}->findByEmail($email);
        if ($user) {
            $email = $user[$userModel]['email'];
        }
        if (!$user || mb_strlen($email) === 0) {
            $this->BcMessage->setError('送信されたメールアドレスは登録されていません。');
            return;
        }
        $password = $this->generatePassword();
        $user[$userModel]['password'] = $password;
        $this->{$userModel}->set($user);

        $dataSource = $this->{$userModel}->getDataSource();
        $dataSource->begin();

        if (!$this->{$userModel}->save(null, ['validate' => false])) {
            $dataSource->roolback();
            $this->BcMessage->setError('新しいパスワードをデータベースに保存できませんでした。');
            return;
        }
        $body = ['email' => $email, 'password' => $password];
        if (!$this->sendMail($email, __d('baser', 'パスワードを変更しました'), $body, ['template' => 'reset_password'])) {
            $dataSource->roolback();
            $this->BcMessage->setError('メール送信時にエラーが発生しました。');
            return;
        }

        $dataSource->commit();

        $this->BcMessage->setSuccess($email . ' 宛に新しいパスワードを送信しました。');
        $this->request->withData($userModel, []);

    }

}
