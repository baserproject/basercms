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
 * ダッシュボード
 * @var array $panels
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcBaser->js(['admin/dashboard/index.bundle'], false);
$this->BcAdmin->setHelp('dashboard_index');
?>


<div class="bca-panel">
  <?php if ($panels): ?>
    <?php foreach($panels as $plugin => $templates): ?>
      <?php foreach($templates as $template): ?>
        <div class="panel-box bca-panel-box">
          <?php if ($template): ?>
            <?php $this->BcBaser->element($plugin . ".Dashboard/" . $template) ?>
          <?php endif ?>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
  <?php endif ?>
</div>
