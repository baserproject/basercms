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

namespace BaserCore\Service\Admin;

use Authentication\Identity;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\Service\UsersService;
use Cake\Core\Configure;
use Cake\Http\Cookie\Cookie;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersService
 * @package BaserCore\Service
 * @property UsersTable $Users
 */
class UserManageService extends UsersService implements UserManageServiceInterface
{

    /**
     * SiteConfigsTrait
     */
    use SiteConfigsTrait;

    /**
     * LoginStoresTable
     * @var LoginStoresTable
     */
    public $LoginStores;

    /**
     * UsersService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->LoginStores = TableRegistry::getTableLocator()->get('BaserCore.LoginStores');
    }

    /**
     * 更新対象データがログインユーザー自身の更新かどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isSelfUpdate(?int $id)
    {
        $loginUser = BcUtil::loginUser();
        return (!empty($id) && !empty($loginUser->id) && $loginUser->id === $id);
    }

    /**
     * 更新ができるかどうか
     * 自身の更新、または、管理者であること
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable(?int $id)
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        } else {
            return ($this->isSelfUpdate($id) || $user->isAdmin());
        }
    }

    /**
     * 削除できるかどうか
     * 管理者であること、また、自身は削除できない
     * @param int $id
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDeletable(?int $id)
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        }
        return ($user->isAdmin() && !$this->isSelfUpdate($id));
    }

    /**
     * ユーザーグループ選択用のリスト
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserGroupList()
    {
        $userGroups = $this->getService(UserGroupsServiceInterface::class);
        return $userGroups->list();
    }

    /**
     * ログインユーザーが自身のユーザーグループを変更しようとしているかどうか
     * @param array $postData
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function willChangeSelfGroup(array $postData)
    {
        $loginUser = BcUtil::loginUser();
        if (empty($loginUser->user_groups)) {
            return false;
        }
        $loginGroupId = Hash::extract($loginUser->user_groups, '{n}.id');
        $postGroupId = array_map('intval', $postData['user_groups']['_ids']);
        return ($loginGroupId !== $postGroupId);
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
    public function getAuthSessionKey($prefix)
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

}
