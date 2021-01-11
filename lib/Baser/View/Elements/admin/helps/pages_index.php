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
 * [ADMIN] ページ一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'ページ管理では、Webページの新規追加や編集・削除などが行えます。') ?></p>
<ul>
	<li><?php echo __d('baser', '新しいページを登録するには、一覧左上の「新規追加」ボタンをクリックします。') ?></li>
	<li><?php echo sprintf(__d('baser', '公開状態を設定する事ができ、公開中のページを確認するには操作欄の %s ボタンをクリックします。'), $this->BcBaser->getImg('admin/icn_tool_check.png')) ?></li>
	<li><?php echo __d('baser', '各ページは分類分け用の「カテゴリー」に属させる事ができ、階層構造のURLを実現できます。') ?></li>
	<li><?php echo __d('baser', '管理画面内では、公開状態、カテゴリ等によりページの検索を行う事ができます。') ?></li>
	<li><?php echo sprintf(__d('baser', '一覧左上の「並び替え」をクリックすると、各データの操作欄に表示される %s マークをドラッグアンドドロップして、公開ページにおけるデータの表示順の変更を行う事ができます。<br /><small>※ この並び順はウィジェットのローカルナビゲーション等に反映されます。'), $this->BcBaser->getImg('admin/sort.png', ['alt' => __d('baser', '並び替え')])) ?></small></li>
	<li><?php echo __d('baser', 'オーサリングツールでの制作に慣れている方向けに、ファイルをアップロードしてデータベースに一括で読み込む機能を備えています。<br />ページを読み込むには、特定のフォルダにページテンプレートをアップロードして、サブメニューの「ページテンプレート読込」を実行します。<br /><a href="https://basercms.net/manuals/etc/5.html" class="outside-link" target="_blank">ページテンプレートの読込について</a>') ?></li>
</ul>
<div class="example-box">
	<p class="head"><?php echo __d('baser', '（例）ページ名「about」として作成したページを表示させる為のURL') ?></p>
	<pre>http://{<?php echo __d('baser', 'baserCMS設置URL') ?>}/about</pre>
	<p class="head"><?php echo __d('baser', '（例）カテゴリ名「company」に属する、ページ名「about」として作成したページを表示させる為のURL') ?></p>
	<pre>http://{<?php echo __d('baser', 'baserCMS設置URL') ?>}/company/about</pre>
</div>
