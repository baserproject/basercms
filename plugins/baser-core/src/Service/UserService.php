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

namespace BaserCore\Service;

use Authentication\Identity;
use BaserCore\Model\Entity\User;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Http\Cookie\Cookie;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;
use DateTime;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserService
 * @package BaserCore\Service
 * @property UsersTable $Users
 * @property LoginStoresTable $LoginStores
 */
class UserService implements UserServiceInterface
{

    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->Users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $this->LoginStores = TableRegistry::getTableLocator()->get('BaserCore.LoginStores');
    }

    /**
     * ユーザーの新規データ用の初期値を含んだエンティティを取得する
     * @return User
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->Users->newEntity([
            'user_groups' => [
                '_ids' => [1]
            ]]);
    }

    /**
     * ユーザーを取得する
     * @param int $id
     * @return User
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): User
    {
        return $this->Users->get($id, [
            'contain' => ['UserGroups'],
        ]);
    }

    /**
     * ユーザー管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        $query = $this->Users->find('all')->contain('UserGroups');

        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        if (!empty($queryParams['user_group_id'])) {
            $query->matching('UserGroups', function($q) use ($queryParams) {
                return $q->where(['UserGroups.id' => $queryParams['user_group_id']]);
            });
        }
        if (!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . $queryParams['name'] . '%']);
        }
        return $query;
    }

    /**
     * ユーザー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $postData, ['validate' => 'new']);
        return $this->Users->saveOrFail($user);
    }

    /**
     * ユーザー情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        if(empty($postData['login_user_id'])) {
            $loginUser = BcUtil::loginUser();
            if(!empty($loginUser['id'])) {
                $postData['login_user_id'] = (string) $loginUser['id'];
            }
        }
        $user = $this->Users->patchEntity($target, $postData);
        return $this->Users->saveOrFail($user);
    }

    /**
     * ユーザー情報を削除する
     * 最後のシステム管理者でなければ削除
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $user = $this->get($id);
        if ($user->isAdmin()) {
            $count = $this->Users
                ->find('all', ['conditions' => ['UsersUserGroups.user_group_id' => Configure::read('BcApp.adminGroupId')]])
                ->join(['table' => 'users_user_groups',
                    'alias' => 'UsersUserGroups',
                    'type' => 'inner',
                    'conditions' => 'UsersUserGroups.user_id = Users.id'])
                ->count();
            if ($count === 1) {
                throw new Exception(__d('baser', '最後のシステム管理者は削除できません'));
            }
        }
        return $this->Users->delete($user);
    }

    /**
     * ユーザーリストを取得する
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->Users->getUserList();
    }

    /**
     * ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @param $id
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(ServerRequest $request, ResponseInterface $response, $id)
    {
        $user = $this->get($id);
        if (!$user) {
            return false;
        }
        $authentication = $request->getAttribute('authentication');
        $authentication->persistIdentity($request, $response, $user);
        return [
            'request' => $request->withAttribute('identity', new Identity($user)),
            'response' => $response
        ];
    }

    /**
     * ログアウト
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function logout(ServerRequest $request, ResponseInterface $response, $id)
    {
        if (!$sessionKey = $this->getAuthSessionKey($request->getParam('prefix'))) {
            return false;
        }

        $this->removeLoginKey($id);
        $this->response = $response->withExpiredCookie(new Cookie(LoginStoresTable::KEY_NAME));

        /** @var \Cake\Http\Session $session */
        $session = $request->getAttribute('session');
        $session->delete($sessionKey);
        $session->renew();
        return [
            'request' => $request->withoutAttribute('identity'),
            'response' => $response
        ];
    }

    /**
     * 認証用のセッションキーを取得
     * @param string $prefix
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    private function getAuthSessionKey($prefix)
    {
        $authSetting = Configure::read('BcPrefixAuth.' . $prefix);
        if (!$authSetting) {
            return false;
        }
        return $authSetting['sessionKey'];
    }

    /**
     * 再ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reLogin(ServerRequest $request, ResponseInterface $response)
    {
        $user = BcUtil::loginUser($request->getParam('prefix'));
        if (!$user) {
            return false;
        }
        $result = $this->logout($request, $response, $user->id);
        if ($result) {
            return $this->login($result['request'], $result['response'], $user->id);
        }
        return false;
    }

    /**
     * ログイン状態の保存のキー送信
     * @param ResponseInterface
     * @param int $id
     * @return ResponseInterface
     * @see https://book.cakephp.org/4/ja/controllers/request-response.html#response-cookies
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCookieAutoLoginKey($response, $id): ResponseInterface
    {
        $loginStore = $this->LoginStores->addKey('Admin', $id);
        return $response->withCookie(Cookie::create(
            LoginStoresTable::KEY_NAME,
            $loginStore->store_key,
            [
                'expires' => new DateTime(LoginStoresTable::EXPIRE),
                'httponly' => true,
                'secure' => true,
            ]
        ));
    }

    /**
     * ログインキーを削除する
     * @param int $id
     * @return int 削除行数
     * @checked
     * @noTodo
     * @unitTest
     */
    public function removeLoginKey($id)
    {
        return $this->LoginStores->removeKey('Admin', $id);
    }

    /**
     * ログイン状態の保存確認
     * @return ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function checkAutoLogin(ServerRequest $request, ResponseInterface $response): ResponseInterface
    {
        $authentication = $request->getAttribute('authentication');
        if(!$authentication) {
            return $response;
        }

        $user = $authentication->getIdentity();
        if ($user !== null) {
            return $response;
        }

        $autoLoginKey = $request->getCookie(LoginStoresTable::KEY_NAME);
        if ($autoLoginKey === null) {
            return $response;
        }

        $loginStore = $this->LoginStores->getEnableLoginStore($autoLoginKey);
        if ($loginStore === null) {
            return $response;
        }

        $user = $this->get($loginStore->user_id);
        if ($user === null) {
            return $response;
        }

        $authentication->persistIdentity($request, $response, $user);
        // キーのリフレッシュ
        $loginStore = $this->LoginStores->refresh('Admin', $loginStore->user_id);
        return $this->setCookieAutoLoginKey($response, $loginStore->user_id);
    }

    /**
     * 代理ログインを行う
     * @param ServerRequest $request
     * @param int $id
     * @param string $referer
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loginToAgent(ServerRequest $request, ResponseInterface $response, $id, $referer = ''): bool
    {
        $user = BcUtil::loginUser($request->getParam('prefix'));
        $this->logout($request, $response, $user->id);
        if($this->login($request, $response, $id)) {
            $session = $request->getSession();
            $session->write('AuthAgent.User', $user);
            $session->write('AuthAgent.referer', $referer);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 代理ログインから元のユーザーに戻る
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|mixed|string
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function returnLoginUserFromAgent(ServerRequest $request, ResponseInterface $response)
    {
        $session = $request->getSession();
        $user = $session->read('AuthAgent.User');
        if (empty($user)) {
            throw new Exception(__d('baser', '対象データが見つかりません。'));
        }
        $currentUser = BcUtil::loginUser($request->getParam('prefix'));
        $this->logout($request, $response, $currentUser->id);
        $this->login($request, $response, $user->id);
        $redirectUrl = $session->read('AuthAgent.referer') ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect'));
        $session->delete('AuthAgent');
        return $redirectUrl;
    }

    /**
     * ログイン情報をリロードする
     *
     * @param ServerRequest $request
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reload(ServerRequest $request)
    {
        $prefix = $request->getParam('prefix');
        $sessionUser = BcUtil::loginUser($prefix);
        if ($sessionUser === null) {
            return true;
        }
        try {
            $user = $this->get($sessionUser->id);
            $session = $request->getSession();
            $sessionKey = Configure::read('BcPrefixAuth.' . $prefix . '.sessionKey');
            $session->write($sessionKey, $user);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * ログインユーザー自身のIDか確認
     * @param int $id
     * @return mixed
     */
    public function isSelf(?int $id)
    {
        $loginUser = BcUtil::loginUser();
        return (!empty($id) && !empty($loginUser->id) && $loginUser->id === $id);
    }

}
