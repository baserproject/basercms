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

use BaserCore\Error\BcException;
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
    public $helpers = ['BaserCore.BcBaser', 'BaserCore.BcAuth', 'BaserCore.BcAdmin'];

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
        return $this->BcAuth->isAgentUser();
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
        if (Configure::read('BcRequest.isUpdater')) return false;
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
            return ['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'edit', $loginUser->id];
        } else {
            return ['prefix' => $this->BcAuth->getCurrentPrefix(), 'controller' => 'Users', 'action' => 'edit', $loginUser->id];
        }
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
                return __d('baser', 'デバッグモード');
            case 'install':
                return __d('baser', 'インストールモード');
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
                return __d('baser', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。');
            case 'install':
                return __d('baser', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。');
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
        if ($this->_View->getName() === 'Installations') {
            return 'install';
        } elseif (Configure::read('BcRequest.isUpdater')) {
            return 'update';
        } elseif ($this->_View->getRequest()->getParam('prefix') === "Admin" || $this->isLoginUrl()) {
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
        $currentSite = $this->_View->getRequest()->getAttribute('currentSite');
        /* @var SitesService $siteService */
        $siteService = $this->getService(SitesServiceInterface::class);
        $content = $siteService->getRootContent($currentSite->id);
        $normalUrl = '/';
        if($content) $normalUrl = $this->BcBaser->getContentsUrl($content->url, true, $currentSite->use_subdomain);
        $links = [
            'install' => Configure::read('BcLinks.installManual'),
            'update' => Configure::read('BcLinks.updateManual'),
            'normal' => $normalUrl,
            'frontAdminAvailable' => ['prefix' => 'Admin', 'controller' => 'dashboard', 'action' => 'index'],
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
            'install' => __d('baser', 'インストールマニュアル'),
            'update' => __d('baser', 'アップデートマニュアル'),
            'normal' => 'サイト表示',
            'frontAdminAvailable' => 'ダッシュボード',
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
            'install' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link'],
            'update' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link'],
            'normal' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'frontAdminAvailable' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false],
            'frontAdminNotAvailable' => ['title' => $this->BcAuth->getCurrentName()],
        ];
        return $options[$this->getLogoType()];
    }

    /**
     * 編集画面リンク出力
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publishLink()
    {
        $this->BcAdmin->publishLink();
    }

}
