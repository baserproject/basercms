<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] コンテンツ一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'コンテンツ管理では、固定ページや、ブログやメールフォームなど、Webページを生成するコンテンツの新規追加や編集・削除などが行えます。') ?></p>
<ul>
	<li><?php echo __d('baser', '一覧は「ツリー形式」と「表形式」を選択する事ができ、新しいコンテンツの追加を行うには、ツリー形式を選択します。また、削除や公開設定についての一括処理や、コンテンツの検索を行うには、表形式を選択します。') ?></li>
	<li><?php echo sprintf(__d('baser', '新しいコンテンツを登録するには、ツリー形式で、フォルダの名称の右側に配置された機能ボタン %s をクリックするか、フォルダを指定してサブメニューの「コンテンツ新規追加」をクリックし機能メニューを表示します。'), $this->BcBaser->getImg('admin/icon_function.png')) ?></li>
	<li><?php echo __d('baser', '機能メニューでは、各コンテンツ追加できる他、指定したコンテンツについて、公開・非公開切り替え、名称変更、編集画面への遷移、コピー、ゴミ箱に入れる等の機能が提供されています。') ?></li>
	<li><?php echo __d('baser', 'ツリー構造において、構成、並び順は、フロントエンド側のメニューに連携します。並び順や、フォルダを変更する場合は、対象コンテンツのアイコンを指定し、ドラッグ＆ドロップで移動する事ができます。<br>※ ドラッグ＆ドロップができるのは、その権限を保つ場合のみです。<br>※ メニューとの連携はテーマがその機能に対応している必要があります。') ?>
	</li>
</ul>
<!--
<div class="example-box">
	<p class="head"></p>
	<pre></pre>
</div>
-->
