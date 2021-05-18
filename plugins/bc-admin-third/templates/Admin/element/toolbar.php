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

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use BaserCore\View\AppView;

/**
 * toolbar
 * @var AppView $this
 */

// JSの出力について、ツールバーはフロントエンドでも利用するため、inlineに出力する
$this->BcBaser->js(['vendor/jquery.fixedMenu', 'vendor/outerClick', 'admin/toolbar.bundle']);
$authName = $this->BcAuth->getCurrentName();
$logoLinkSettings = [
    'install' => [
        'text' => __d('baser', 'インストールマニュアル'),
        'link' => Configure::read('BcApp.installManual'),
        'options' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link']
    ],
    'update' => [
        'text' => __d('baser', 'アップデートマニュアル'),
        'link' => Configure::read('BcApp.updateManual'),
        'options' => ['target' => '_blank', 'class' => 'bca-toolbar__logo-link']
    ],
    'normal' => [
        'text' => $this->BcBaser->siteConfig['formal_name'],
        'link' => '/',
        'options' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false]
    ],
    'frontAdminAvailable' => [
        'text' => $this->BcBaser->siteConfig['formal_name'],
        'link' => ['admin' => true, 'controller' => 'dashboard', 'action' => 'index'],
        'options' => ['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false]
    ],
    'frontAdminNotAvailable' => [
        'text' => $authName,
        'link' => $this->BcAuth->getCurrentLoginRedirectUrl(),
        'options' => ['title' => $authName]
    ],
];
$modeLabelSettings = [
    'debug' => [
        'title' => __d('baser', 'デバッグモード'),
        'description' => __d('baser', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。')
    ],
    'install' => [
        'title' => __d('baser', 'インストールモード'),
        'description' => __d('baser', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。')
    ]
];

$isCurrentUserAdminAvailable = $this->BcAuth->isCurrentUserAdminAvailable();
$loginUrl = $this->BcAuth->getCurrentLoginUrl();
$currentPrefix = $this->BcAuth->getCurrentPrefix();
$loginUser = $this->BcAuth->getCurrentLoginUser();
$session = $this->getRequest()->getSession();
$currentUrl = $this->request->getPath();
$isLoginUrl = ($currentUrl === $loginUrl) ? true : false;
$isFront = ($currentPrefix === 'front') ? true : false;
$logo = $this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol']);
if ($this->name === 'Installations') {
    $logoLinkKey = 'install';
} elseif (Configure::read('BcRequest.isUpdater')) {
    $logoLinkKey = 'update';
} elseif ($this->request->getParam('prefix') === "Admin" || $isLoginUrl) {
    $logoLinkKey = 'normal';
} else {
    if ($isCurrentUserAdminAvailable) {
        $logoLinkKey = 'frontAdminAvailable';
    } else {
        $logoLinkKey = 'frontAdminNotAvailable';
    }
}
$modeLabelKey = '';
if (!$loginUrl || !$isLoginUrl) {
    if (Configure::read('debug')) {
        $modeLabelKey = 'debug';
    } elseif (BcUtil::isInstallMode()) {
        $modeLabelKey = 'install';
    }
}
if($isFront) {
    $loginUrl = ['controller' => 'users', 'action' => 'login'];
}
if($loginUser) {
    if($isCurrentUserAdminAvailable) {
        $accountEditUrl = ['admin' => true, 'controller' => 'users', 'action' => 'edit', $loginUser->id];
    } else {
        $accountEditUrl = [$currentPrefix => true, 'controller' => 'users', 'action' => 'edit', $loginUser->id];
    }
}
?>


<div id="ToolBar" class="bca-toolbar">
    <div id="ToolbarInner" class="clearfix bca-toolbar__body">

        <div class="bca-toolbar__logo">
            <?php $this->BcBaser->link(
                $logo . '<span class="bca-toolbar__logo-text">' . $logoLinkSettings[$logoLinkKey]['text'] . '</span>',
                $logoLinkSettings[$logoLinkKey]['link'],
                $logoLinkSettings[$logoLinkKey]['options']
            ) ?>
        </div>

        <div id="ToolMenu" class="bca-toolbar__tools">
            <?php if ($this->BcBaser->existsEditLink() && !isset($this->request->getQuery['preview'])): ?>
                <div class="bca-toolbar__tools-button bca-toolbar__tools-button-edit">
                    <?php $this->BcBaser->editLink() ?>
                </div>
            <?php endif ?>
            <?php if ($this->BcBaser->existsPublishLink()): ?>
                <div class="bca-toolbar__tools-button bca-toolbar__tools-button-publish">
                    <?php $this->BcBaser->publishLink() ?>
                </div>
            <?php endif ?>
            <?php if ($modeLabelKey): ?>
                <div class="bca-toolbar__tools-mode">
                    <span id="DebugMode" class="bca-debug-mode" title="<?php echo h($modeLabelSettings[$modeLabelKey]['description']) ?>">
                        <?php echo h($modeLabelSettings[$modeLabelKey]['title']) ?>
                    </span>
                </div>
            <?php endif ?>
        </div>

        <div id="UserMenu" class="bca-toolbar__users">
            <ul class="clearfix">
                <li>
                    <?php if ($this->BcAuth->isAdminLogin()): ?>
                        <?php $this->BcBaser->link(h($this->BcBaser->getUserName($loginUser)) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title', 'escapeTitle' => false]) ?>
                        <ul>
                            <?php if ($this->BcAuth->isAgentUser()): ?>
                                <li><?php $this->BcBaser->link(__d('baser', '元のユーザーに戻る'), ['admin' => false, 'controller' => 'users', 'action' => 'back_agent']) ?></li>
                            <?php endif ?>

                            <?php if ($isCurrentUserAdminAvailable || !$isFront): ?>
                                <li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), $accountEditUrl) ?></li>
                            <?php endif ?>

                            <li><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $this->BcAuth->getCurrentLogoutUrl()) ?></li>
                        </ul>

                    <?php elseif ($this->name !== 'Installations' && !$isLoginUrl && !Configure::read('BcRequest.isUpdater')): ?>
                        <?php $this->BcBaser->link(__d('baser', 'ログインしていません ') . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title', 'escapeTitle' => false]) ?>
                        <ul>
                            <li><?php $this->BcBaser->link(__d('baser', 'ログイン'), $loginUrl) ?></li>
                        </ul>
                    <?php endif ?>
                </li>

                <?php if ($this->BcAuth->isAdminLogin() && $isCurrentUserAdminAvailable): ?>
                    <li><?php $this->BcBaser->link(__d('baser', 'キャッシュクリア'), ['admin' => true, 'controller' => 'Utilities', 'action' => 'clear_cache'], ['confirm' => __d('baser', 'キャッシュクリアします。いいですか？')]) ?></li>
                <?php endif ?>
            </ul>
        </div>

    </div>
</div>
