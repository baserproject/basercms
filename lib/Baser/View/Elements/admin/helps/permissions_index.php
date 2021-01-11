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
 * [ADMIN] アクセス制限設定一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'サイト運営者には必要最低限のメニューしか表示しないなど、ユーザーグループごとのアクセス制限をかける事でシンプルでわかりやすいインターフェイスを実現する事ができます。<br />一覧左上の「新規追加」ボタンより新しいルールを追加します。') ?></p>
<ul>
	<li><?php echo __d('baser', 'ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。') ?></li>
	<li><?php echo __d('baser', '複数のルールを追加した場合は、上から順に設定が上書きされ、下にいくほど優先されます。') ?></li>
	<li><?php echo __d('baser', 'URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。') ?></li>
	<li><?php echo __d('baser', '管理者グループ「admins」には、アクセス制限の設定はできません。') ?></li>
	<li><?php echo sprintf(__d('baser', '一覧左上の「並び替え」をクリックすると、その際に各データの操作欄に表示される %s マークをドラッグアンドドロップして行の並び替えができます。'), $this->BcBaser->getImg('admin/sort.png', ['alt' => __d('baser', '並び替え')])) ?></li>
</ul>
<div class="example-box">
	<div class="head"><?php echo __d('baser', '（例）ページ管理全体は許可しないが、特定のページ「NO: ２」のみ許可を与える場合') ?></div>
	<ol>
		<li><?php echo __d('baser', '1つ目のルールとして、　/admin/pages/*　を「不可」として追加します。') ?></li>
		<li><?php echo __d('baser', '2つ目のルールとして、　/admin/pages/edit/2　を「可」として追加します。') ?></li>
	</ol>
</div>
