<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var BcAppView $this
 */
$dblogs = $this->BcAdmin->getDblogs(5);
?>

<h2 class="bca-panel-box__title"><?php echo __d('baser', '最近の動き') ?></h2>
<div id="DblogList">
  <?php if ($dblogs): ?>
    <div class="bca-update-log">
      <ul class="clear bca-update-log__list">
        <?php foreach ($dblogs as $dblog): ?>
          <li class="bca-update-log__list-item">
            <span class="date">
              <?php echo $this->BcTime->format($dblog->created, 'yyyy-MM-dd') ?>
            </span>
            <small>
              <?php echo $this->BcTime->format($dblog->created, 'HH:mm:ss') ?>&nbsp;
              <?php if ($dblog->user): ?>
                <?php echo '[' . h($dblog->user->name) . ']' ?>
              <?php endif ?>
            </small><br/>
            <?php echo nl2br(h($dblog->message)) ?>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
    <?php if ($dblogs->count()): ?>
      <div class="align-right">
        <?php $this->BcBaser->link(__d('baser', '> 全てのログを見る'), ['controller' => 'dblogs', 'action' => 'index']) ?>
      </div>
    <?php endif ?>
  <?php endif ?>
</div>
