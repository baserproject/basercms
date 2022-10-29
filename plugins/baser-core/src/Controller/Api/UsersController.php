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

namespace BaserCore\Controller\Api;

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Service\UsersServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;

/**
 * Class UsersController
 *
 * @property AuthenticationComponent $Authentication
 */
class UsersController extends BcApiController
{

    /**
     * Initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * ログイン
     * @param UsersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(UsersServiceInterface $service)
    {
        $result = $this->Authentication->getResult();
        $json = [];
        if (!$result->isValid() || !$json = $this->getAccessToken($this->Authentication->getResult())) {
            $this->setResponse($this->response->withStatus(401));
        } else {
            $redirect = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
            $user = $result->getData();
            $service->removeLoginKey($user->id);
            if ($this->request->is('ssl') && $this->request->getData('saved')) {
                $this->response = $service->setCookieAutoLoginKey($this->response, $user->id);
            }
            $this->BcMessage->setInfo(__d('baser', 'ようこそ、' . $user->getDisplayName() . 'さん。'));
            $json['redirect'] = $redirect;
        }
        $this->set('json', $json);
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * リフレッシュトークン取得
     * @checked
     * @noTodo
     * @unitTest
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
     * @param UsersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UsersServiceInterface $service)
    {
        $this->set([
            'users' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['users']);
    }

    /**
     * ユーザー情報取得
     * @param UsersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UsersServiceInterface $service, $id)
    {
        $this->set([
            'user' => $service->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['user']);
    }

    /**
     * ユーザー情報登録
     * @param UsersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UsersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $user = $service->create($this->request->getData());
            $message = __d('baser', 'ユーザー「{0}」を追加しました。', $user->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
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
     * @param UsersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UsersServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $user = $service->get($id);
        try {
            $user = $service->update($user, $this->request->getData());
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
     * @param UsersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UsersServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $service->get($id);
        try {
            if ($service->delete($id)) {
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
