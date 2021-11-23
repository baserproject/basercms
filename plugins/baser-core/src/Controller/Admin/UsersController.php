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
use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\User;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Core\Exception\Exception;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\UserServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use Cake\Datasource\Exception\RecordNotFoundException;
use Authentication\Controller\Component\AuthenticationComponent;

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
    public function login(UserServiceInterface $userService)
    {
        $this->set('savedEnable', $this->request->is('ssl'));
        $result = $this->Authentication->getResult();
        if ($this->request->is('post')) {
            if ($result->isValid()) {
                $target = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
                $user = $result->getData();
                $userService->removeLoginKey($user->id);
                if ($this->request->is('ssl') && $this->request->getData('saved') == '1') {
                    // 自動ログイン保存
                    $this->response = $userService->setCookieAutoLoginKey($this->response, $user->id);
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
    public function login_agent(UserServiceInterface $userService, $id): ?Response
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
        $userService->loginToAgent($this->request, $this->response, $id, $this->referer());
        return $this->redirect($this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
    }

    /**
     * 代理ログイン解除
     * @return Response
     * @unitTest
     * @noTodo
     * @checked
     */
    public function back_agent(UserServiceInterface $userService)
    {
        try {
            $redirectUrl = $userService->returnLoginUserFromAgent($this->request, $this->response);
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
    public function logout(UserServiceInterface $userService)
    {
        /* @var User $user */
        $user = $this->Authentication->getIdentity();
        $userService->logout($this->request, $this->response, $user->id);
        $this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

    /**
     * ログインユーザーリスト
     * 管理画面にログインすることができるユーザーの一覧を表示する
     * @param UserServiceInterface $userService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserServiceInterface $userService, SiteConfigServiceInterface $siteConfigService): void
    {
        $this->setViewConditions('User', ['default' => ['query' => [
            'num' => $siteConfigService->getValue('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Users.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->request
        ]);
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }

        $this->set('users', $this->paginate($userService->getIndex($this->request->getQueryParams())));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * ログインユーザー新規追加
     * 管理画面にログインすることができるユーザーの各種情報を新規追加する
     * @param UserServiceInterface $userService
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserServiceInterface $userService)
    {
        if ($this->request->is('post')) {
            $user = $userService->create($this->request->getData());
            if (!$user->getErrors()) {
                // EVENT Users.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'user' => $user
                ]);
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を追加しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $user = $userService->getNew();
        }
        $this->set('user', $user);
    }

    /**
     * ログインユーザー編集
     * 管理画面にログインすることができるユーザーの各種情報を編集する
     * @param UserServiceInterface $userService
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserServiceInterface $userService, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $user = $userService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $userService->update($user, $this->request->getData());
            if (!$user->getErrors()) {
                $this->dispatchLayerEvent('afterEdit', [
                    'user' => $user
                ]);
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * ログインユーザー削除
     * 管理画面にログインすることができるユーザーを削除する
     * @param UserServiceInterface $userService
     * @param string|null $id User id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(UserServiceInterface $userService, $id = null)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $user = $userService->get($id);
        try {
            if ($userService->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー: {0} を削除しました。', $user->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
