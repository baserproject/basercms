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
/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $firstAccess
 */
if (empty($firstAccess)) return;
?>


<div id="FirstMessage" class="em-box bca-em-box" style="text-align:left">
  <?php echo __d('baser_core', 'baserCMSへようこそ。') ?><br>
  <ul style="font-weight:normal;font-size:14px;">
    <li><?php echo __d('baser_core', '右上のヘルプより画面のヘルプが確認できます。') ?></li>
    <li><?php echo __d('baser_core', 'まずは、画面左のメニュー、「コンテンツ管理」よりWebサイトの全体像を確認しましょう。') ?></li>
  </ul>
</div>
