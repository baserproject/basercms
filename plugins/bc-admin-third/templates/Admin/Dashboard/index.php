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

/**
 * ダッシュボード
 * @var array $panels
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcBaser->js(['admin/dashboard/index.bundle'], false);
$this->BcAdmin->setHelp('dashboard_index');
?>


<div class="bca-panel">
  <?php if ($panels): ?>
    <?php foreach($panels as $key => $templates): ?>
      <?php foreach($templates as $template): ?>
        <div class="panel-box bca-panel-box">
          <?php if ($template): ?>
            <?php $this->BcBaser->element("Dashboard/" . $template) ?>
          <?php endif ?>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
  <?php endif ?>
</div>
