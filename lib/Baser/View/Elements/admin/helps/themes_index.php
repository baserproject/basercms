<?php
/**
 * [ADMIN] テーマ一覧　ヘルプ
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

<p>ここではテーマを切り替えたり、テーマファイルを閲覧、編集したりとテーマの管理を行う事ができます。<br />
	なお、コアテンプレートとは、baserCMSコアで準備しているテンプレートで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。</p>
<ul>
	<li>テーマを切り替えるには、対象テーマの <?php $this->BcBaser->img('admin/icn_tool_apply.png') ?> ボタンをクリックします。</li>
	<li>テーマを丸ごとコピーするには、対象テーマの <?php $this->BcBaser->img('admin/icn_tool_copy.png') ?> ボタンをクリックします。</li>
	<li>テーマファイルを閲覧、編集する場合は、対象テーマの <?php $this->BcBaser->img('admin/icn_tool_edit.png') ?> ボタンをクリックします。</li>
	<li>テーマを削除するには、対象テーマの  <?php $this->BcBaser->img('admin/icn_tool_delete.png') ?> ボタンをクリックします。</li>
</ul>
<p>テーマを追加する場合には、
	/app/webroot/theme/{テーマ名}/ としてテーマフォルダを作成し、 そのフォルダの中にCakePHPのテンプレートファイルやcss、javascriptファイル等を配置します。<br />
	<small>※ テーマ名には半角小文字のアルファベットを利用します。</small>
</p>