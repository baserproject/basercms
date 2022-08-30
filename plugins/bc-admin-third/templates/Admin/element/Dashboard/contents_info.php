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
 * @var BcAdminAppView $this
 * @var array $contentsInfo
 * @checked
 * @unitTest
 * @noTodo
 */
?>

<h2 class="bca-panel-box__title"><?php echo __d('baser', 'コンテンツ情報') ?></h2>
<div id="ContentInfo">
  <?php if ($contentsInfo): ?>
    <div class="bca-content-info">
      <?php foreach($contentsInfo as $site): ?>
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
