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
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * @var BcAdminAppView $this
 * @checked
 * @noTodo
 */
$sites = $this->BcAdminDashboard->getContentsInfo();
?>

<h2 class="bca-panel-box__title"><?php echo __d('baser', 'コンテンツ情報') ?></h2>
<div id="ContentInfo">
  <?php if ($sites): ?>
    <div class="bca-content-info">
      <?php foreach($sites as $site): ?>
        <h3 class="bca-content-info__title"><?php echo h($site['display_name']) ?></h3>
        <ul class="bca-content-info__list">
          <li class="bca-content-info__list-item">
            <?php echo sprintf(__d('baser', '公開中： %s ページ'), $site['published']) ?><br>
            <?php echo sprintf(__d('baser', '非公開： %s ページ'), $site['unpublished']) ?><br>
            <?php echo sprintf(__d('baser', '合計： %s ページ'), $site['total']) ?>
          </li>
        </ul>
      <?php endforeach ?>
    </div>
  <?php endif ?>
</div>
