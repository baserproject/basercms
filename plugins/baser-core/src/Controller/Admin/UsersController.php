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
use BaserCore\Service\Admin\UserManageServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
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
     * initialize
     * ログインページ認証除外
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * 管理画面へログインする
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(UserManageServiceInterface $userManage)
    {
        $this->set('savedEnable', $this->request->is('ssl'));
        $result = $this->Authentication->getResult();
        if ($this->request->is('post')) {
            if ($result->isValid()) {
                $target = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
                $user = $result->getData();
                $userManage->removeLoginKey($user->id);
                if ($this->request->is('ssl') && $this->request->getData('saved') == '1') {
                    // 自動ログイン保存
                    $this->response = $userManage->setCookieAutoLoginKey($this->response, $user->id);
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
     * 別のユーザにログインできる
     * @param string|null $id User id.
     * @return Response|void Redirects
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login_agent(UserManageServiceInterface $userManage, $id): ?Response
    {
        // 特権確認
        if (BcUtil::isSuperUser() === false) {
            throw new ForbiddenException();
        }
        // 既に代理ログイン済み
        if (BcUtil::isAgentUser()) {
            $this->BcMessage->setError(__d('baser', '既に代理ログイン中のため失敗しました。'));
            return $this->redirect(['action' => 'index']);
        }
        $userManage->loginToAgent($this->request, $this->response, $id, $this->referer());
        return $this->redirect($this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
    }

    /**
     * 代理ログイン解除
     * @return Response
     * @unitTest
     * @noTodo
     * @checked
     */
    public function back_agent(UserManageServiceInterface $userManage)
    {
        try {
            $redirectUrl = $userManage->returnLoginUserFromAgent($this->request, $this->response);
            $this->BcMessage->setInfo(__d('baser', '元のユーザーに戻りました。'));
            return $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            $this->BcMessage->setError($e->getMessage());
            return $this->redirect($this->referer());
        }
    }

    /**
     * ログイン状態のセッションを破棄する
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout(UserManageServiceInterface $userManage)
    {
        /* @var User $user */
        $user = $this->Authentication->getIdentity();
        $userManage->logout($this->request, $this->response, $user->id);
        $this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

    /**
     * ログインユーザーリスト
     * 管理画面にログインすることができるユーザーの一覧を表示する
     * @param UserManageServiceInterface $userManage
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserManageServiceInterface $userManage): void
    {
        $this->setViewConditions('User', ['default' => ['query' => [
            'num' => $userManage->getSiteConfig('admin_list_num'),
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
     * 管理画面にログインすることができるユーザーの各種情報を新規追加する
     * @param UserManageServiceInterface $userManage
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserManageServiceInterface $userManage)
    {
        if ($this->request->is('post')) {
            $user = $userManage->create($this->request->getData());
            if (!$user->getErrors()) {
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
     * 管理画面にログインすることができるユーザーの各種情報を編集する
     * @param UserManageServiceInterface $userManage
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
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
            if (!BcUtil::isAdminUser() && $userManage->willChangeSelfGroup($this->getRequest()->getData())) {
                $this->BcMessage->setError(__d('baser', '自分のアカウントのグループは変更できません。'));
            } else {
                $user = $userManage->update($user, $this->request->getData());
                if (!$user->getErrors()) {
                    $this->getEventManager()->dispatch(new Event('Controller.Users.afterEdit', $this, [
                        'user' => $user
                    ]));
                    if ($userManage->isSelfUpdate($user->id)) {
                        $this->Authentication->setIdentity($userManage->get($user->id));
                    }
                    $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                    return $this->redirect(['action' => 'edit', $user->id]);
                } else {
                    $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
                }
            }
        }
        $this->set('user', $user);
    }

    /**
     * ログインユーザー削除
     * 管理画面にログインすることができるユーザーを削除する
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
