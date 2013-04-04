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

<p>ユーザー管理ではログインする事ができるユーザーの管理を行う事ができます。<br />
	新しいユーザーを登録するには一覧左上の「新規追加」ボタンをクリックします。</p>
<p>システム管理グループのユーザーでログインしている場合、一覧の<?php $bcBaser->img('admin/icn_tool_login.png', array('alt'=> 'ログイン', 'title' => 'ログイン')) ?>より簡単に他のユーザーとして再ログインする事ができます（代理ログイン）。<br />
	他のユーザーの「よく使う項目」を登録する場合などに便利です。</p>
<p>元のユーザーに戻るには、画面上部のユーザー名部分から「元のユーザーに戻る」をクリックします。<br />
<small>※ 代理ログインの対象は、システム管理グループ以外のユーザーとなります。</small></p>