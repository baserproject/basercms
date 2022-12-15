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
 * [ADMIN] ブログ記事 一覧　ヘルプ
 * @checked
 * @noTodo
 * @unitTest
 */
?>

<p><?php echo __d('baser', '記事の管理が行えます。') ?></p>
<ul>
  <li><?php echo __d('baser', '新しい記事を登録するには、画面上の「新規追加」ボタンをクリックします。') ?></li>
  <li><?php echo __d('baser', 'ブログの表示を確認するには、ツールバーの「サイト確認」をクリックします。') ?></li>
  <li><?php echo sprintf(__d('baser', '各記事の表示を確認するには、対象記事の %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="preview"></i>') ?></li>
  <li><?php echo __d('baser', 'ブログのコメントを確認するには、サブメニューの「コメント」をクリックするか、各記事のコメント欄の数字をクリックします。') ?></li>
  <li><?php echo __d('baser', 'ブログのカテゴリを登録するには、サブメニューの「カテゴリ」をクリックします。') ?></li>
</ul>
