<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧　ヘルプ
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

<p>ここでは各テーマファイルの閲覧、編集、削除等を行う事ができます。<br />
	なお、テーマ「core」は、baserCMSの核となるテーマで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。</p>
<ul>
	<li>上層のフォルダへ移動するには、
		<?php $bcBaser->img('up.gif',array('alt'=>'上へ','width'=>18)) ?>
		ボタンをクリックします。（現在の位置がテーマフォルダの最上層の場合は表示されません）</li>
	<li>新しいフォルダを作成するには、「フォルダ新規作成」ボタンをクリックします。</li>
	<li>新しいテーマファイルを作成するには、「ファイル新規作成」ボタンをクリックします。</li>
	<li>ご自分のパソコン内のファイルをアップロードするには、「選択」ボタンをクリックし、対象のファイルを選択します。</li>
	<li>テーマファイルをコピーするには、対象ファイルの<?php $bcBaser->img('admin/icn_tool_copy.png') ?>をクリックします。</li>
	<li>テーマファイルを閲覧、編集する場合は、対象ファイルの<?php $bcBaser->img('admin/icn_tool_edit.png') ?>をクリックします。</li>
	<li>テーマファイルを削除するには、対象ファイルの<?php $bcBaser->img('admin/icn_tool_delete.png') ?>をクリックします。</li>
	<li>テーマファイルを現在のテーマにコピーするには、対象ファイル・フォルダの<?php $bcBaser->img('admin/icn_tool_view.png') ?>をクリックし、その後表示される画面下の「現在のテーマにコピー」をクリックします。（core テーマのみ）</li>
</ul>
<p>テーマファイルの種類は次の６つとなります。</p>
<ul>
	<li>レイアウト：Webページの枠組となるテンプレートファイル</li>
	<li>エレメント：共通部品となるテンプレートファイル</li>
	<li>コンテンツ：Webページのコンテンツ部分のテンプレートファイル</li>
	<li>CSS：カスケーディングスタイルシートファイル</li>
	<li>イメージ：写真や背景等の画像ファイル</li>
	<li>Javascript：Javascriptファイル</li>
</ul>
