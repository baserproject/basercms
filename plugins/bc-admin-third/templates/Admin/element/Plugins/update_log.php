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
 * プラグインアップデートのログ
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $log
 */
?>


<div class="bca-panel-box" id="UpdateLog">
  <h2 class="bca-main__heading" data-bca-heading-size="lg">
    <?php echo __d('baser_core', 'ログ') ?>
  </h2>
  <?php echo $this->BcAdminForm->control('log', [
    'type' => 'textarea',
    'value' => $log,
    'style' => 'width:99%;height:200px;font-size:12px',
    'readonly' => 'readonly'
  ]); ?>
</div>
