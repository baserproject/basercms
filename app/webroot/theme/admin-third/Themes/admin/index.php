<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ一覧
 */
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', 'コピーに失敗しました。'),
	'alertMessage2' => __d('baser', '削除に失敗しました。'),
	'confirmMessage1' => __d('baser', "このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。"),
	'confirmMessage2' => __d('baser', '<p><strong>初期データを読み込みます。よろしいですか？</strong></p><br><p>※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。<br>※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。</p>'),
	'confirmTitle1' => __d('baser', '初期データ読込')
], ['escape' => false]);
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/baser_ajax_data_list_config',
	'admin/themes/index'
]);
?>


<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'themes', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>

<div id="tabs">
	<ul>
		<li><a href="#DataList"><?php echo __d('baser', '所有テーマ') ?></a></li>
		<li><a href="#BaserMarket"><?php echo __d('baser', 'baserマーケット') ?></a></li>
	</ul>
	<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('themes/index_list') ?></div>
	<div id="BaserMarket">
		<div
			style="padding:20px;text-align:center;"><?php $this->BcBaser->img('admin/ajax-loader.gif', ['alt' => 'Loading...']) ?></div>
	</div>
</div>
