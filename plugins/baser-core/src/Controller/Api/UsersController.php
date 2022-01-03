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

namespace BaserCore\Controller\Api;

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Service\UserServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;

/**
 * Class UsersController
 *
 * https://localhost/baser/api/baser-core/users/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 * @property AuthenticationComponent $Authentication
 */
class UsersController extends BcApiController
{

    /**
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * ログイン
     */
    public function login(UserServiceInterface $users)
    {
        $result = $this->Authentication->getResult();
        $json = [];
        if (!$result->isValid() || !$json = $this->getAccessToken($this->Authentication->getResult())) {
            $this->setResponse($this->response->withStatus(401));
        } else {
            $redirect = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
            $user = $result->getData();
            $users->removeLoginKey($user->id);
            if ($this->request->is('ssl') && $this->request->getData('saved')) {
                $this->response = $users->setCookieAutoLoginKey($this->response, $user->id);
            }
            $this->BcMessage->setInfo(__d('baser', 'ようこそ、' . $user->getDisplayName() . 'さん。'));
            $json['redirect'] = $redirect;
        }
        $this->set('json', $json);
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * リフレッシュトークン取得
     */
    public function refresh_token()
    {
        $json = [];
        $payload = $this->Authentication->getAuthenticationService()->getAuthenticationProvider()->getPayload();
        if ($payload->token_type !== 'refresh_token' || !$json = $this->getAccessToken($this->Authentication->getResult())) {
            $this->setResponse($this->response->withStatus(401));
        }
        $this->set('json', $json);
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * ユーザー情報一覧取得
     * @param UserServiceInterface $users
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserServiceInterface $users)
    {
        $this->set([
            'users' => $this->paginate($users->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['users']);
    }

    /**
     * ユーザー情報取得
     * @param UserServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UserServiceInterface $users, $id)
    {
        $this->set([
            'user' => $users->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['user']);
    }

    /**
     * ユーザー情報登録
     * @param UserServiceInterface $users
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserServiceInterface $users)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $user = $users->create($this->request->getData());
            $message = __d('baser', 'ユーザー「{0}」を追加しました。', $user->name);
        } catch (\Exception $e) {
            $user = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'user' => $user,
            'errors' => $user->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'user', 'errors']);
    }

    /**
     * ユーザー情報編集
     * @param UserServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserServiceInterface $users, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $user = $users->get($id);
        try {
            $user = $users->update($user, $this->request->getData());
            $message = __d('baser', 'ユーザー「{0}」を更新しました。', $user->name);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'user' => $user,
            'errors' => $user->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message', 'errors']);
    }

    /**
     * ユーザー情報削除
     * @param UserServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserServiceInterface $users, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $users->get($id);
        try {
            if ($users->delete($id)) {
                $message = __d('baser', 'ユーザー: {0} を削除しました。', $user->name);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message']);
    }

}
