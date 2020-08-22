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
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\View\Helper;

/**
 * Class BcAuthHelper
 * @package BaserCore\View\Helper
 * @property BcBaserHelper $BcBaser
 * @property ServerRequest $request
 * @uses BcAuthHelper
 */
class BcAuthHelper extends Helper {

    /**
     * Helper
     * @var array
     */
    public $helpers = ['BcBaser'];

    /**
     * 現在認証プレフィックスを取得する
     * @return string currentPrefix
     */
    public function getCurrentPrefix () {
		if (!empty($this->request->getParam('prefix'))) {
			$currentPrefix = $this->request->getParam('prefix');
		} else {
			$currentPrefix = 'front';
		}
		return $currentPrefix;
    }

    /**
     * 現在の認証プレフィックスの設定を取得
     * @return array 認証プレフィックス設定
     */
    public function getCurrentPrefixSetting() {
        return Configure::read('BcAuthPrefix.' . $this->getCurrentPrefix());
    }

    /**
     * 現在の認証プレフィックスのログインURLを取得
     * @return string
     */
    public function getCurrentLoginUrl() {
        return preg_replace('/^\//', '', $this->getCurrentPrefixSetting()['loginAction']);
    }

    /**
     * 現在のユーザーに許可された認証プレフィックスを取得する
     * @return array
     */
    public function getCurrentUserPrefixSettings() {
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
     */
    public function isCurrentUserAdminAvailable() {
        return in_array('admin', $this->getCurrentUserPrefixSettings());
    }

    /**
     * 現在のユーザーのログインアクションを取得する
     * TODO: 未実装
     * @return string
     */
    public function getCurrentLoginAction() {
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
     */
    public function getCurrentName() {
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
     */
    public function isAdminLogin() {
        // TODO 実装要
        return true;
    }

    /**
     * 現在のログアウトURL
     * @return mixed
     */
    public function getCurrentLogoutUrl() {
        return $this->getCurrentPrefixSetting()['logoutAction'];
    }

    /**
     * 現在のログイン後のリダイレクトURL
     * @return mixed
     */
    public function getCurrentLoginRedirectUrl() {
        return $this->getCurrentPrefixSetting()['loginRedirect'];
    }

    /**
     * 現在のログインユーザー
     * @return array
     * @todo 実装要
     */
    public function getCurrentLoginUser() {
        return [true];
    }

}
