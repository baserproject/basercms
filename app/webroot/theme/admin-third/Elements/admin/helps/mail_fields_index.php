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
	<li><?php echo sprintf(__d('baser', '表左上の「%s」をクリックすると、各フィールドに表示される「%s」マークをドラッグアンドドロップして公開ページでの並び順を変更する事ができます。'), '<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>並び替え', '<span class="sort-handle ui-sortable-handle" style="cursor: move;"><i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>ドラッグ可能</span>') ?></li>
	<li><?php echo sprintf(__d('baser', 'フィールドの設定をそのままコピーするにはコピーしたいフィールドのアクション欄にある %s をクリックします。'), '<i class="bca-btn-icon" data-bca-btn-type="copy"></i>') ?></li>
	<li><?php echo __d('baser', 'メールフォームより受信した内容は、サブメニューの「受信メールCSVダウンロード」よりダウンロードする事ができ、Microsoft Excel 等の表計算ソフトで確認する事ができます。') ?></li>
</ul>
