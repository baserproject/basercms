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

use BaserCore\Service\SitesService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\View\Helper;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BcToolbarHelper
 * @property BcBaserHelper $BcBaser
 * @property BcAuthHelper $BcAuth
 * @property BcAdminHelper $BcAdmin
 */
#[\AllowDynamicProperties]
class BcToolbarHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcBaser',
        'BaserCore.BcAuth',
        'BaserCore.BcAdmin'
    ];

    /**
     * 編集画面へのリンクが利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableEditLink(): bool
    {
        $request = $this->_View->getRequest();
        return ($this->BcAdmin->existsEditLink() && !$request->getQuery('preview'));
    }

    /**
     * 公開ページへのリンクが利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailablePublishLink(): bool
    {
        return $this->BcAdmin->existsPublishLink();
    }

    /**
     * 現在のページで固定ページの新規登録が有効かどうか
     *
     * フロントページでコンテンツフォルダを表示している事が条件
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップ
     */
    public function isAvailableAddLink(): bool
    {
        return $this->BcAdmin->existsAddLink();
    }

    /**
     * モードの表示が利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableMode(): bool
    {
        return (bool)$this->getMode();
    }

    /**
     * キャッシュクリアが利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableClearCache(): bool
    {
        return $this->BcAuth->isCurrentUserAdminAvailable();
    }

    /**
     * 元のユーザーに戻るが利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableBackAgent(): bool
    {
        return ($this->BcAuth->isAgentUser() && $this->BcAuth->isCurrentUserAdminAvailable());
    }

    /**
     * ログインが利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableLogin(): bool
    {
        if (BcUtil::loginUser()) return false;
        if ($this->_View->getName() === 'Installations') return false;
        if ($this->isLoginUrl()) return false;
        return true;
    }

    /**
     * アカウント編集が利用可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableAccountSetting(): bool
    {
        return ($this->BcAuth->isCurrentUserAdminAvailable());
    }

    /**
     * アカウント編集画面へのURL取得
     * @return array|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAccountSettingUrl()
    {
        $loginUser = $this->BcAuth->getCurrentLoginUser();
        if (!$loginUser) return '';

        if ($this->BcAuth->isCurrentUserAdminAvailable()) {
            $prefix = 'Admin';
        } else {
            $prefix = $this->BcAuth->getCurrentPrefix();
        }

        $model = Configure::read('BcPrefixAuth.' . $prefix . '.userModel');
        list($plugin,) = pluginSplit($model);
        if(!$plugin) $plugin = 'BaserCore';

        return [
            'plugin' => $plugin,
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => 'edit',
            $loginUser->id
        ];
    }

    /**
     * ログインURL取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLoginUrl(): string
    {
        if ($this->BcAuth->isCurrentUserAdminAvailable()) {
            return $this->BcAuth->getLoginUrl('Admin');
        } else {
            return $this->BcAuth->getCurrentLoginUrl();
        }
    }

    /**
     * ログアウトURL取得
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoutUrl()
    {
        if ($this->BcAuth->isCurrentUserAdminAvailable()) {
            return $this->BcAuth->getLogoutUrl('Admin');
        } else {
            return $this->BcAuth->getCurrentLogoutUrl();
        }
    }

    /**
     * 現在のモードを取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMode(): string
    {
        if(!BcUtil::isInstalled()) return '';
        if (!$this->isLoginUrl()) {
            if (Configure::read('debug')) {
                return 'debug';
            } elseif (BcUtil::isInstallMode()) {
                return 'install';
            }
        }
        return '';
    }

    /**
     * 現在のモードのタイトル取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeTitle(): string
    {
        switch($this->getMode()) {
            case 'debug':
                return __d('baser_core', 'デバッグモード');
            case 'install':
                return __d('baser_core', 'インストールモード');
        }
        return '';
    }

    /**
     * 現在のモードの説明文取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeDescription(): string
    {
        switch($this->getMode()) {
            case 'debug':
                return __d('baser_core', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。');
            case 'install':
                return __d('baser_core', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。');
        }
        return '';
    }

    /**
     * 現在の画面がログイン画面のURLかどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isLoginUrl(): bool
    {
        return ($this->_View->getRequest()->getPath() === $this->BcAuth->getCurrentLoginUrl());
    }

    /**
     * ロゴのタイプを取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoType(): string
    {
        if ($this->getView()->getName() === 'Installations') {
            return 'install';
        } elseif ($this->getView()->getRequest()->getParam('controller') === 'Plugins'
            && $this->getView()->getRequest()->getParam('action') === 'update'
            && empty($this->getView()->getRequest()->getParam('pass'))
        ) {
            return 'update';
        } elseif ($this->getView()->getRequest()->getParam('prefix') === "Admin" || $this->isLoginUrl()) {
            return 'normal';
        } else {
            if ($this->BcAuth->isCurrentUserAdminAvailable()) {
                return 'frontAdminAvailable';
            } else {
                return 'frontAdminNotAvailable';
            }
        }
    }

    /**
     * ロゴのリンクを取得
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoLink()
    {
        if(!BcUtil::isInstalled()) return Configure::read('BcLinks.installManual');
        $currentSite = $this->_View->getRequest()->getAttribute('currentSite');
        $normalUrl = '/';
        if($currentSite) {
            /* @var SitesService $siteService */
            $siteService = $this->getService(SitesServiceInterface::class);
            $content = $siteService->getRootContent($currentSite->id);
            if($content) $normalUrl = $this->BcBaser->getContentsUrl($content->url, true, $currentSite->use_subdomain);
        }
        $links = [
            'install' => '',
            'update' => Configure::read('BcLinks.updateManual'),
            'normal' => $normalUrl,
            'frontAdminAvailable' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'dashboard', 'action' => 'index'],
            'frontAdminNotAvailable' => $this->BcAuth->getCurrentLoginRedirectUrl(),
        ];
        return $links[$this->getLogoType()];
    }

    /**
     * ロゴのテキストを取得
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoText()
    {
        $texts = [
            'install' => __d('baser_core', 'インストールマニュアル'),
            'update' => __d('baser_core', 'アップデートマニュアル'),
            'normal' => __d('baser_core', 'サイト表示'),
            'frontAdminAvailable' => __d('baser_core', 'ダッシュボード'),
            'frontAdminNotAvailable' => $this->BcAuth->getCurrentName(),
        ];
        return $texts[$this->getLogoType()];
    }

    /**
     * ロゴのリンクのオプションを取得
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLogoLinkOptions()
    {
        $options = [
            'install' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'update' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'normal' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'frontAdminAvailable' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'frontAdminNotAvailable' => ['class' => 'bca-toolbar__logo-link', 'title' => $this->BcAuth->getCurrentName(), 'escapeTitle' => false],
        ];
        return $options[$this->getLogoType()];
    }

    /**
     * 編集画面リンク出力
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function editLink()
    {
        $this->BcAdmin->editLink();
    }

    /**
     * 公開画面リンク出力
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publishLink()
    {
        $this->BcAdmin->publishLink();
    }

    /**
     * 固定ページ新規追加画面へのリンクを出力する
     *
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためスキップ
     */
    public function addLink()
    {
        $this->BcAdmin->addLink();
    }

}
