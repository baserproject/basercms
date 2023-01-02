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

use BaserCore\View\BcAdminAppView;

/**
 * Contents Menu
 *
 * BcAdminHelper::contentsMenu() より呼び出される
 *
 * @var BcAdminAppView $this
 * @var bool $isHelp
 * @var bool $isLogin
 */

if (!$isLogin) return;
$contentsMenu =  [];
// EVENT beforeContentsMenu
$event = $this->dispatchLayerEvent('beforeContentsMenu', ['contentsMenu' => $contentsMenu], ['class' => '', 'plugin' => '']);
if ($event !== false) {
  $contentsMenu = ($event->getResult() === null || $event->getResult() === true)? $event->getData('contentsMenu') : $event->getResult();
}
// EVENT PluginName.ControllerName.beforeContentsMenu
$globalEvent = $this->dispatchLayerEvent('beforeContentsMenu', ['contentsMenu' => $contentsMenu]);
if ($globalEvent !== false) {
  $contentsMenu = ($globalEvent->getResult() === null || $globalEvent->getResult() === true)? $globalEvent->getData('contentsMenu') : $globalEvent->getResult();
}

?>
  <div id="ContentsMenu" class="bca-content-menu">
    <ul>
      <?php if($contentsMenu): ?>
        <?php foreach ($contentsMenu as $menu):?>
          <li class="bca-content-menu__item">
            <?php echo $menu; ?>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if ($isHelp): ?>
        <li class="bca-content-menu__item">
          <?php // TODO: button要素に変更 ?>
          <?php $this->BcBaser->link(__d('baser', 'ヘルプ'), 'javascript:void(0)', ['id' => 'BtnMenuHelp', 'class' => 'bca-content-menu__link bca-icon--help']) ?>
        </li>
      <?php endif ?>
      <?php if ($isSuperUser): ?>
        <li class="bca-content-menu__item">
          <?php $this->BcBaser->element('Permissions/dialog') ?>
          <?php // TODO: button要素に変更 ?>
          <?php $this->BcBaser->link(__d('baser', 'ルール追加'), 'javascript:void(0)', ['id' => 'BtnMenuPermission', 'class' => 'bca-content-menu__link bca-icon--permission']) ?></li>
      <?php endif ?>
    </ul>
  </div>
