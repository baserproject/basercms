<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン一覧　ヘルプ
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


<p>baserCMSのプラグインの管理を行います。<br />
	初期状態では、メールフォーム・フィードリーダー・ブログの３つのプラグインが標準プラグインとして同梱されており、
	インストールも完了しています。各プラグインの<?php $bcBaser->img('admin/icn_tool_manage.png') ?>から各プラグインの管理が行えます。</p>
<div class="example-box">
	<div class="head">新しいプラグインのインストール方法</div>
	<ol>
		<li>app/plugins/ フォルダに、入手したプラグインのフォルダをアップロードします。</li>
		<li>プラグイン一覧に、新しいプラグインが表示されますので、その行の<?php $bcBaser->img('admin/icn_tool_install.png') ?>をクリックします。</li>
		<li>登録画面が表示されますので「登録」ボタンをクリックしてインストールを完了します。</li>
	</ol>
</div>