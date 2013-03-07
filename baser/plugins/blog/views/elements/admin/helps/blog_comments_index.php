<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 コメント一覧　ヘルプ
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

<p>ブログ記事に対するコメントの管理が行えます。</p>
<ul>
	<li>コメントが投稿された場合、サイト基本設定で設定された管理者メールアドレスに通知メールが送信されます。</li>
	<li>コメントが投稿された場合、コメント承認機能を利用している場合は、コメントのステータスは「非公開」となっています。
		内容を確認して問題なければ、<?php $bcBaser->img('admin/icn_tool_publish.png') ?>をクリックします。</li>
</ul>