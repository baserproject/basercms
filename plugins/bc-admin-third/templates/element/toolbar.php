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

use BaserCore\Model\Entity\Site;
use BaserCore\Model\Entity\User;
use BaserCore\View\BcAdminAppView;

/**
 * toolbar
 * @var BcAdminAppView $this
 * @var Site $currentSite
 * @var array $otherSites
 * @var User $loginUser
 * @var string $currentAdminTheme
 * @checked
 * @noTodo
 * @unitTest
 */
// JSの出力について、ツールバーはフロントエンドでも利用するため、inlineに出力する
$this->BcBaser->js([$currentAdminTheme . '.vendor/jquery.fixedMenu', $currentAdminTheme . '.vendor/outerClick', $currentAdminTheme . '.admin/toolbar.bundle']);
?>


<div id="ToolBar" class="bca-toolbar">
  <div id="ToolbarInner" class="clearfix bca-toolbar__body">

    <div class="bca-toolbar__logo">
      <?php $this->BcBaser->link(
        $this->BcBaser->getImg($currentAdminTheme . '.admin/logo_icon.svg', [
          'alt' => '',
          'width' => '24',
          'height' => '21',
          'class' => 'bca-toolbar__logo-symbol',
        ]) .
        '<span class="bca-toolbar__logo-text">' . $this->BcToolbar->getLogoText() . '</span>',
        $this->BcToolbar->getLogoLink(),
        $this->BcToolbar->getLogoLinkOptions()
      ) ?>
    </div>

    <div id="ToolMenu" class="bca-toolbar__tools">
      <?php if ($otherSites): ?>
        <ul class="clearfix">
          <li>
            <?php $this->BcBaser->link(
              h($currentSite->display_name) . ' ' .
              $this->BcBaser->getImg($currentAdminTheme . '.admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']),
              'javascript:void(0)', [
              'class' => 'title',
              'escapeTitle' => false
            ]) ?>
            <ul>
              <?php foreach($otherSites as $key => $value): ?>
                <li>
                  <?php $this->BcBaser->link($value, [
                    'prefix' => 'Admin',
                    'plugin' => 'BaserCore',
                    'controller' => 'Contents',
                    'action' => 'index',
                    '?' => ['site_id' => $key]
                  ]) ?>
                </li>
              <?php endforeach ?>
            </ul>
          </li>
        </ul>
      <?php endif ?>
      <?php if ($this->BcToolbar->isAvailableEditLink()): ?>
        <div class="bca-toolbar__tools-button bca-toolbar__tools-button-edit">
          <?php $this->BcToolbar->editLink() ?>
        </div>
      <?php endif ?>
      <?php if ($this->BcToolbar->isAvailablePublishLink()): ?>
        <div class="bca-toolbar__tools-button bca-toolbar__tools-button-publish">
          <?php $this->BcToolbar->publishLink() ?>
        </div>
      <?php endif ?>
      <?php if ($this->BcToolbar->isAvailableMode()): ?>
        <div class="bca-toolbar__tools-mode">
          <span id="DebugMode" class="bca-debug-mode" title="<?php echo h($this->BcToolbar->getModeDescription()) ?>">
              <?php echo h($this->BcToolbar->getModeTitle()) ?>
          </span>
        </div>
      <?php endif ?>
    </div>

    <?php if ($this->BcAuth->isAdminLogin()): ?>
      <div id="UserMenu" class="bca-toolbar__users">
        <ul class="clearfix">
          <li>
            <?php $this->BcBaser->link(
              h($this->BcBaser->getUserName($loginUser)) . ' ' .
              $this->BcBaser->getImg($currentAdminTheme . '.admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']),
              'javascript:void(0)', [
              'class' => 'title',
              'escapeTitle' => false
            ]) ?>
            <ul>
              <?php if ($this->BcToolbar->isAvailableBackAgent()): ?>
                <li>
                  <?php $this->BcBaser->link(
                    __d('baser', '元のユーザーに戻る'),
                    ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'users', 'action' => 'back_agent']
                  ) ?>
                </li>
              <?php endif ?>
              <?php if ($this->BcToolbar->isAvailableAccountSetting()): ?>
                <li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), $this->BcToolbar->getAccountSettingUrl()) ?></li>
              <?php endif ?>
              <li><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $this->BcToolbar->getLogoutUrl(), ['id' => 'BtnLogout']) ?></li>
            </ul>
          </li>
          <?php if ($this->BcToolbar->isAvailableLogin()): ?>
            <li>
              <?php $this->BcBaser->link(
                __d('baser', 'ログインしていません ') .
                $this->BcBaser->getImg($currentAdminTheme . '.admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']),
                'javascript:void(0)',
                ['class' => 'title', 'escapeTitle' => false]
              ) ?>
              <ul>
                <li><?php $this->BcBaser->link(__d('baser', 'ログイン'), $this->BcToolbar->getLoginUrl()) ?></li>
              </ul>
            </li>
          <?php endif ?>

          <?php if ($this->BcToolbar->isAvailableClearCache()): ?>
            <li>
              <?php $this->BcBaser->link(
                __d('baser', 'キャッシュクリア'),
                ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'Utilities', 'action' => 'clear_cache'],
                ['confirm' => __d('baser', 'キャッシュクリアします。いいですか？')]
              ) ?>
            </li>
          <?php endif ?>
        </ul>
      </div>
    <?php endif ?>
  </div>
</div>
