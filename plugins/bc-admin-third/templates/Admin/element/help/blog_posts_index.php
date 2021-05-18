<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 一覧　ヘルプ
 */
?>

<p><?php echo __d('baser', '記事の管理が行えます。') ?></p>
<ul>
	<li><?php echo __d('baser', '新しい記事を登録するには、画面下の「新規追加」ボタンをクリックします。') ?></li>
	<li><?php echo __d('baser', 'ブログの表示を確認するには、サブメニューの「公開ページ確認」をクリックします。') ?></li>
	<li><?php echo sprintf(__d('baser', '各記事の表示を確認するには、対象記事の %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="preview"></i>') ?></li>
	<li><?php echo __d('baser', 'ブログのコメントを確認するには、サブメニューの「コメント一覧」をクリックするか、各記事のコメント欄の数字をクリックします。') ?></li>
	<li><?php echo __d('baser', 'ブログのカテゴリを登録するには、サブメニューの「新規カテゴリを登録」をクリックします。') ?></li>
</ul>
