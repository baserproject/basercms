<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ一覧　ヘルプ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<p>ページ管理では、Webページの新規追加や編集・削除などが行えます。</p>
<ul>
	<li>新しいページを登録するには、一覧左上の「新規追加」ボタンをクリックします。</li>
	<li>公開状態を設定する事ができ、公開中のページを確認するには操作欄の <?php $bcBaser->img('admin/icn_tool_check.png') ?> ボタンをクリックします。</li>
	<li>各ページは分類分け用の「カテゴリー」に属させる事ができ、階層構造のURLを実現できます。</li>
	<li>管理画面内では、公開状態、カテゴリ等によりページの検索を行う事ができます。</li>
	<li>一覧左上の「並び替え」をクリックすると、各データの操作欄に表示される<?php $bcBaser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして、公開ページにおけるデータの表示順の変更を行う事ができます。<br />
	<small>※ この並び順はウィジェットのローカルナビゲーション等に反映されます。</small></li>
	<li>オーサリングツールでの制作に慣れている方向けに、ファイルをアップロードしてデータベースに一括で読み込む機能を備えています。<br />
		ページを読み込むには、特定のフォルダにページテンプレートをアップロードして、サブメニューの「ページテンプレート読込」を実行します。<br />
		<a href="http://basercms.net/manuals/etc/5.html" class="outside-link" target="_blank">ページテンプレートの読込について</a></li>
</ul>
<div class="example-box">
	<p class="head">（例）ページ名「about」として作成したページを表示させる為のURL</p>
	<pre>http://{baserCMS設置URL}/about</pre>
	<p class="head">（例）カテゴリ名「company」に属する、ページ名「about」として作成したページを表示させる為のURL</p>
	<pre>http://{baserCMS設置URL}/company/about</pre>
</div>