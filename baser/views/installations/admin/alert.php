<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストール警告ページ
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<p>インストールを開始するにはデバッグモードが -1 である必要があります。</p>
<p>デバッグモードを変更するには次の手順のとおり操作してください。</p>

<ul>
	<li>次のファイルを開きます。<br />
		<pre>/app/config/install.php</pre>
	<li>
	<li>core.phpより次の行を見つけます。<br />
		<pre>Configure::write('debug', 0);</pre>
	</li>
	<li>0 の部分を、 -1 に書き換えます。</li>
	<li>編集したファイルをサーバーにアップロードします。</li>
</ul>

<ul><li><?php $bcBaser->link('baserCMSを初期化するにはコチラから','/installations/reset') ?></li></ul>
