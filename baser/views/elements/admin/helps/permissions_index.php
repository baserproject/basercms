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

<p>サイト運営者には必要最低限のメニューしか表示しないなど、ユーザーグループごとのアクセス制限をかける事でシンプルでわかりやすいインターフェイスを実現する事ができます。<br />
	一覧左上の「新規追加」ボタンより新しいルールを追加します。</p>
<ul>
	<li>ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。</li>
	<li>複数のルールを追加した場合は、上から順に設定が上書きされ、下にいくほど優先されます。</li>
	<li>URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。</li>
	<li>管理者グループ「admins」には、アクセス制限の設定はできません。</li>
	<li>一覧左上の「並び替え」をクリックすると、その際に各データの操作欄に表示される <?php $bcBaser->img('sort.png',array('alt'=>'並び替え')) ?> マークをドラッグアンドドロップして行の並び替えができます。</li>
</ul>
<div class="example-box">
	<div class="head">（例）ページ管理全体は許可しないが、特定のページ「NO: ２」のみ許可を与える場合</div>
	<ol>
		<li>1つ目のルールとして、　/admin/pages/*　を「不可」として追加します。</li>
		<li>2つ目のルールとして、　/admin/pages/edit/2　を「可」として追加します。</li>
	</ol>
</div>
