<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールコンテンツ一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'メールフォームの各フィールド（項目）の管理が行えます。') ?></p>
<ul>
	<li><?php echo sprintf(__d('baser', '表左上の「並び替え」をクリックすると、各フィールドの操作欄に表示される%sマークをドラッグアンドドロップして公開ページでの並び順を変更する事ができます。'), $this->BcBaser->getImg('admin/sort.png', ['alt' => __d('baser', '並び替え')])) ?></li>
	<li><?php echo sprintf(__d('baser', 'フィールドの設定をそのままコピーするにはコピーしたいフィールドの操作欄にある %s をクリックします。'), $this->BcBaser->img('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー')])) ?></li>
	<li><?php echo __d('baser', 'メールフォームより受信した内容は、サブメニューの「受信メールCSVダウンロード」よりダウンロードする事ができ、Microsoft Excel 等の表計算ソフトで確認する事ができます。') ?></li>
</ul>
