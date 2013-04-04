<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アクセス制限設定一覧　ヘルプ
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


<p>ユーザーグループごとのアクセス制限を登録します。</p>
<ul>
	<li>ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。</li>
	<li>URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。</li>
</ul>
<div class="example-box">
	<p class="head">（例）ページ管理全体を許可しない設定</p>
	<p>　/admin/pages/*</p>
	<p class="head">（例）ブログコンテンツNO:2 の管理を許可しない設定</p>
	<p>　/admin/blog/*/*/2</p>
</div>