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
?>


<?php if ($isLogin): ?>
  <div id="ContentsMenu" class="bca-content-menu">
    <ul>
      <li class="bca-content-menu__item">
        <?php // TODO: button要素に変更 ?>
        <?php //$this->BcBaser->link(__d('baser', 'お気に入りに追加'), 'javascript:void(0)', ['id' => 'BtnFavoriteAdd', 'data-bca-fn' => 'BtnFavoriteAdd', 'class' => 'bca-content-menu__link bca-icon--plus-square']) ?></li>
      <?php if ($isHelp): ?>
        <li class="bca-content-menu__item">
          <?php // TODO: button要素に変更 ?>
          <?php $this->BcBaser->link(__d('baser', 'ヘルプ'), 'javascript:void(0)', ['id' => 'BtnMenuHelp', 'class' => 'bca-content-menu__link bca-icon--help']) ?></li>
      <?php endif ?>
      
      <?php if ($isSuperUser): ?>
        <li class="bca-content-menu__item">
          <?php $this->BcBaser->element('permission') ?>
          <?php // TODO: button要素に変更 ?>
          <?php // TODO アクセス制限を実装 ?>
          <?php $this->BcBaser->link(__d('baser', '制限'), 'javascript:void(0)', ['id' => 'BtnMenuPermission', 'class' => 'bca-content-menu__link bca-icon--permission']) ?></li>
      <?php endif ?>
    </ul>
  </div>
<?php endif ?>
