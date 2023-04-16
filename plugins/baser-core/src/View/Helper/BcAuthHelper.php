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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcAuthHelper
 * @property BcBaserHelper $BcBaser
 * @uses BcAuthHelper
 */
class BcAuthHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

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
            $currentPrefix = BcUtil::getRequestPrefix($request);
        }
        $users = BcUtil::getLoggedInUsers();
        if($users && empty($users[$currentPrefix])) {
            foreach($users as $key => $user) {
                return $key;
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
    public function getCurrentPrefixSetting(): ?array
    {
        return $this->getPrefixSetting($this->getCurrentPrefix());
    }

    /**
     * 認証プレフィックスの設定を取得
     * @param $prefix
     * @return array|false[]|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPrefixSetting($prefix)
    {
        $setting = Configure::read('BcPrefixAuth.' . $prefix);
        if(!empty($setting['disabled'])) return [];
        return $setting;
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
        return $this->getLoginUrl($this->getCurrentPrefix());
    }

    /**
     * 認証プレフィックスのログインURLを取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLoginUrl($prefix)
    {
        $setting = $this->getPrefixSetting($prefix);
        if(!empty($setting['loginAction'])) {
            return Router::url($setting['loginAction']);
        } else {
            return '';
        }
    }

    /**
     * 現在のユーザーに許可された認証プレフィックスを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentUserPrefixes(): array
    {
        $user = BcUtil::loginUser();
        if(!$user || !$user->user_groups) return [];
		$prefixes = [];
		foreach($user->user_groups as $userGroup) {
		    $prefix = explode(',', $userGroup->auth_prefix);
		    $prefixes = array_merge($prefixes, $prefix);
		}
        return $prefixes;
    }

    /**
     * 現在のユーザーが管理画面の利用が許可されているかどうか
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isCurrentUserAdminAvailable(): bool
    {
        return (in_array('Admin', $this->getCurrentUserPrefixes()) &&
            BcUtil::loginUserFromSession());
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
        if (!empty($currentPrefixSetting['name']) && $this->getCurrentPrefix() !== 'Front') {
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
        return $this->getLogoutUrl($this->getCurrentPrefix());
    }

    /**
     * ログアウトURLを取得する
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoutUrl($prefix, $isConverted = false)
    {
        $setting = $this->getPrefixSetting($prefix);
        if(!empty($setting['logoutAction'])) {
            return $isConverted? Router::url($setting['logoutAction']) : $setting['logoutAction'];
        } else {
            return '';
        }
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
        $setting = $this->getCurrentPrefixSetting();
        if(!empty($setting['loginRedirect'])) {
            return Router::url($setting['loginRedirect']);
        } else {
            return '';
        }
    }

    /**
     * 現在のログインユーザー
     * @return EntityInterface
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
     */
    public function isSuperUser(): bool
    {
        return BcUtil::isSuperUser();
    }

    /**
     * 管理ユーザログイン状態判別
     *
     * @return boolean
     * @checked
     * @noTodo
     */
    public function isAdminUser(): bool
    {
        return BcUtil::isAdminUser();
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
