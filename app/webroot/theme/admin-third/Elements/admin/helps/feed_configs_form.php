<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Feed
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定一覧　ヘルプ
 */
?>


<p><?php echo __d('baser', 'フィード設定の基本項目を入力します。<br>フィードごとにデザインを変更する場合には、画面下の「詳細設定」をクリックしてテンプレート名を変更します。<br><small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br>') ?></p>
<ul>
	<li><?php echo __d('baser', '一つの設定につき、フィードは複数登録する事ができます。複数登録した場合は、複数のフィードを合わせた上で日付順に並び替えられます。') ?></li>
	<li><?php echo __d('baser', 'フィードを追加するには、画面下の「フィード一覧」の「新規追加」ボタンをクリックします。') ?></li>
</ul>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="section">
		<h3 id="headHowTo"><?php echo __d('baser', 'フィードの読み込み方法') ?></h3>
		<p><?php echo __d('baser', '以下のjavascriptを読み込みたい場所に貼り付けてください。') ?></p>
		<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php $this->BcBaser->js('/feed/ajax/' . $this->request->data['FeedConfig']['id']) ?>
	</textarea>
		<br/>
		<p><?php echo __d('baser', 'また、フィードの読み込みにはjQueryが必要ですので事前に読み込んでおく必要があります。') ?></p>
		<h4><?php echo __d('baser', 'jQueryの読み込み例') ?></h4>
		<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)"
				  readonly="readonly"><?php echo $this->BcHtml->script('admin/vendors/jquery-2.1.4.min', ['once' => false]) ?></textarea>
	</div>
<?php endif ?>
