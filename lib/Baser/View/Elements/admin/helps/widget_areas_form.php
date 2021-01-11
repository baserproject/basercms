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
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', '一つのウィジェットエリアは、左側の「利用できるウィジェット」からお好きなウィジェットを複数選択して作成する事ができます。') ?></p>
<ul>
	<li><?php echo __d('baser', 'まず、わかりやすい「ウィジェットエリア名」を決めて入力します。（例）サイドバー等') ?></li>
	<li><?php echo __d('baser', '「エリア名を保存する」ボタンをクリックすると「利用できるウィジェット」と「利用中のウィジェット」の二つの領域が表示されます') ?></li>
	<li><?php echo __d('baser', '「利用できるウィジェット」の中から利用したいウィジェットをドラッグして「利用中のウィジェット」の中でドロップします。') ?></li>
	<li><?php echo __d('baser', 'ウィジェットの設定欄が開きますので必要に応じて入力し「保存」ボタンをクリックします。') ?></li>
</ul>
<h5><?php echo __d('baser', 'ポイント') ?></h5>
<ul>
	<li><?php echo __d('baser', '「利用中のウィジェット」はドラッグアンドドロップで並び替える事ができます。') ?></li>
	<li><?php echo __d('baser', '一時的に利用しない場合は、削除せずにウィジェット設定の「利用する」チェックを外しておくと同じ設定のまま後で利用する事ができます。') ?></li>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<li><?php echo __d('baser', 'システム設定より設定できる標準ウィジェットエリアの他、個別にウィジェットを配置する場合は、テンプレートや、ページ記事中（ソース）に次のコードを貼り付けます。') ?></li>
	<?php endif ?>
</ul>
<?php if ($this->request->action == 'admin_edit'): ?>
	<pre>&lt;?php $this->BcBaser->element('widget_area', array('no'=> <?php echo $this->BcForm->value('WidgetArea.id') ?> )) ?&gt;</pre>
<?php endif ?>
