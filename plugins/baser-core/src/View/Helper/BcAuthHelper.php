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

namespace BaserCore\View\Helper;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAuthHelper
 * @package BaserCore\View\Helper
 * @property BcBaserHelper $BcBaser
 * @uses BcAuthHelper
 */
class BcAuthHelper extends Helper
{

    /**
     * Helper
     * @var array
     */
    public $helpers = ['BcBaser'];

    /**
     * 現在認証プレフィックスを取得する
     * @return string currentPrefix
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentPrefix(): string
    {
        $currentPrefix = '';
        $request = $this->_View->getRequest();
        if (!empty($request)) {
            if (!empty($request->getParam('prefix'))) {
                $currentPrefix = $request->getParam('prefix');
            } else {
                $currentPrefix = 'front';
            }
        }
        return $currentPrefix;
    }

    /**
     * 現在の認証プレフィックスの設定を取得
     * @return array 認証プレフィックス設定
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentPrefixSetting(): array
    {
        return Configure::read('BcPrefixAuth.' . $this->getCurrentPrefix());
    }

    /**
     * 現在の認証プレフィックスのログインURLを取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentLoginUrl(): string
    {
        return Router::url($this->getCurrentPrefixSetting()['loginAction']);
    }

    /**
     * 現在のユーザーに許可された認証プレフィックスを取得する
     * @return array
     */
    public function getCurrentUserPrefixSettings(): array
    {
        // TODO: 現在のログインユーザーのセッションキーを取得
        // $sessionKey = BcUtil::getLoginUserSessionKey();
        // >>>
//        $sessionKey = 'admin';
        // <<<
//		$currentUserPrefixes = [];
        // TODO 取り急ぎ
        // >>>
//		if ($this->Session->check('Auth.' . $sessionKey . '.UserGroup.auth_prefix')) {
//			$currentUserAuthPrefixes = explode(',', $this->Session->read('Auth.' . $sessionKey . '.UserGroup.auth_prefix'));
//		}
//        $currentUserPrefixes = ['admin'];
        // <<<
        return ['admin'];
    }

    /**
     * 現在のユーザーが管理画面の利用が許可されているかどうか
     * @return bool
     * @checked
     * @unitTest
     */
    public function isCurrentUserAdminAvailable(): bool
    {
        return in_array('admin', $this->getCurrentUserPrefixSettings());
    }

    /**
     * 現在のユーザーのログインアクションを取得する
     * TODO: 未実装
     * @return string
     */
    public function getCurrentLoginAction(): string
    {
//        $currentAuthPrefixSetting = $this->BcAuth->getCurrentPrefixSetting();
//        if ($this->isCurrentUserAdminAvailable()) {
//            $logoutAction = Configure::read('BcAuthPrefix.admin.logoutAction');
//        } else {
//            $logoutAction = $currentAuthPrefixSetting['logoutAction'];
//        }
//        return $logoutAction;
        return '';
    }

    /**
     * 認証名を取得する
     * フロントの場合はサイト名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentName()
    {
        $currentPrefixSetting = $this->getCurrentPrefixSetting();
        if (!empty($currentPrefixSetting['name']) && $this->getCurrentPrefix() !== 'front') {
            $name = $currentPrefixSetting['name'];
        } elseif (isset($this->BcBaser->siteConfig['formal_name'])) {
            $name = $this->BcBaser->siteConfig['formal_name'];
        } else {
            $name = '';
        }
        return $name;
    }

    /**
     * 管理画面にログインしているかどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdminLogin()
    {
        return (bool)BcUtil::loginUser();
    }

    /**
     * 現在のログアウトURL
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentLogoutUrl()
    {
        return Router::url($this->getCurrentPrefixSetting()['logoutAction']);
    }

    /**
     * 現在のログイン後のリダイレクトURL
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentLoginRedirectUrl()
    {
        return Router::url($this->getCurrentPrefixSetting()['loginRedirect']);
    }

    /**
     * 現在のログインユーザー
     * @return Entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentLoginUser()
    {
        return BcUtil::loginUser();
    }

    /**
     * 特権ユーザログイン状態判別
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isSuperUser(): bool
    {
        return BcUtil::isSuperUser();
    }

    /**
     * 代理ログイン状態判別
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAgentUser(): bool
    {
        return BcUtil::isAgentUser();
    }

}
