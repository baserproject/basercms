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
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * @var BcAdminAppView $this
 * @var array $dblogs
 * @checked
 * @unitTest
 * @noTodo
 */
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
        <?php $this->BcBaser->link(__d('baser', '> 全てのログを見る'), [
          'controller' => 'dblogs',
          'action' => 'index'
        ]) ?>
      </div>
    <?php endif ?>
  <?php endif ?>
</div>
