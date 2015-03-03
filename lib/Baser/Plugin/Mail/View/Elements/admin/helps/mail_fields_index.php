<?php
/**
 * [ADMIN] メールコンテンツ一覧　ヘルプ
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>

<p>メールフォームの各フィールド（項目）の管理が行えます。</p>
<ul>
	<li>表左上の「並び替え」をクリックすると、各フィールドの操作欄に表示される<?php $this->BcBaser->img('admin/sort.png', array('alt' => '並び替え')); ?>マークをドラッグアンドドロップして公開ページでの並び順を変更する事ができます。</li>
	<li>フィールドの設定をそのままコピーするにはコピーしたいフィールドの操作欄にある <?php $this->BcBaser->img('admin/icn_tool_copy.png', array('alt' => 'コピー')); ?> をクリックします。</li>
	<li>メールフォームより受信した内容は、サブメニューの「受信メールCSVダウンロード」よりダウンロードする事ができ、Microsoft Excel 等の表計算ソフトで確認する事ができます。</li>
</ul>