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
 * Help
 *
 * BcAdminHelper::help() より呼び出される
 *
 * @var BcAdminAppView $this
 * @var string $help
 */
?>


<div id="Help" class="clearfix bca-help">

  <h2 class="head bca-help__title">
    <i class="bca-icon--question-circle" data-bca-btn-size="md"></i>
    <?php echo __d('baser', 'ヘルプ') ?>
  </h2>

  <div class="body bca-help__body">
    <?php $this->BcBaser->element('help/' . $help) ?>
  </div>

  <!-- / #Help --></div>
