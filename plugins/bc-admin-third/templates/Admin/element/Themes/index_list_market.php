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
 * [ADMIN] テーマ一覧　テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @var array $baserThemes
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<script>
  $(function () {
    // Ajaxで読み込む前提のため外部ファイルには出さずここに記載
    $(".theme-popup").colorbox({inline: true, width: "60%"});
  });
</script>

<ul class="list-panel bca-list-panel">
  <?php if (!empty($baserThemes)): ?>
    <?php $key = 0 ?>
    <?php foreach($baserThemes as $data): ?>
      <?php $this->BcBaser->element('Themes/index_row_market', ['data' => $data, 'key' => $key++]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="no-data"><?php echo __d('baser', 'baserマーケットのテーマを読み込めませんでした。') ?></li>
  <?php endif; ?>
</ul>
