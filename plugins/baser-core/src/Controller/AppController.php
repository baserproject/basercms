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

namespace BaserCore\Controller;

use App\Controller\AppController as BaseController;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Model\Table\UsersTable;
use Cake\Http\Cookie\Cookie;
use DateTime;

/**
 * Class AppController
 * @package BaserCore\Controller
 */
class AppController extends BaseController
{
    /**
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();

        var_dump("aaa");
        exit;

        // ログイン状態の保存確認
        $this->checkAutoLogin();
    }

    /**
     * ログイン状態の保存確認
     *
     * @return void
     */
    private function checkAutoLogin(): void
    {
        // ログイン状態の保存確認
        $this->loadComponent('Authentication.Authentication');
        $user = $this->Authentication->getIdentity();
        if ($user !== null) {
            return;
        }

        $autoLoginKey = $this->request->getCookie(LoginStoresTable::KEY_NAME);
        if ($autoLoginKey === null) {
            return;
        }

        $this->loadModel('BaserCore.LoginStores');
        $loginStore = $this->LoginStores->getEnableLoginStore($autoLoginKey);
        if ($loginStore === null) {
            return;
        }

        $this->loadModel('BaserCore.Users');
        $user = $this->Users->getLoginFormatData($loginStore->user_id);
        if ($user === null) {
            return;
        }

        $this->Authentication->setIdentity($user);
        // キーのリフレッシュ
        $loginStore = $this->LoginStores->refresh('Admin', $loginStore->user_id);
        $this->setCookieAutoLoginKey($loginStore->store_key);
    }

    /**
     * ログイン状態の保存のキー送信
     *
     * @return void
     */
    public function setCookieAutoLoginKey($key): void
    {
        // https://book.cakephp.org/4/ja/controllers/request-response.html#response-cookies
        $this->response = $this->response->withCookie(Cookie::create(
            LoginStoresTable::KEY_NAME,
            $key,
            [
                'expires' => new DateTime(LoginStoresTable::EXPIRE),
                'httponly' => true,
                'secure' => true,
            ]
        ));
    }
}
