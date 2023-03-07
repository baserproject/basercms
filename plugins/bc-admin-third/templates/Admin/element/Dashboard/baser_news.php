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
 * @var array $baserNews
 * @checked
 * @note
 * @unitTest
 */
?>

<h2 class="bca-panel-box__title"><?php echo __d('baser_core', 'baserCMSニュース') ?></h2>

<?php if($baserNews) ?>
<div id="feeds1" style="display: block;">
  <div class="bca-feed">
    <ul class="bca-feed__list">
      <?php
      $i = 0;
      foreach($baserNews as $post):
        $i++;
      ?>
      <li class="bca-feed__list-item first feed01">
        <span class="date bca-feed__list-item-date">
            <?php echo $this->BcTime->format($post['pubDate'], 'yyyy.MM.dd') ?>
        </span><br>
        <span class="title bca-feed__list-item-title">
          <?php $this->BcBaser->link($post['title'], $post['link'], ['target' => '_blank']) ?>
        </span>
      </li>
        <?php if($i >= 3) break ?>
      <?php endforeach ?>
    </ul>
  </div>
</div>

<p class="bca-panel-box__message">
  <small><?php echo __d('baser_core', 'baserCMSについて、不具合の発見・改善要望がありましたら<a href="https://forum.basercms.net" target="_blank">ユーザーズフォーラム</a> よりお知らせください。') ?></small>
</p>
