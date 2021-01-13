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
 * @var BcAppview $this
 * [ADMIN] プラグイン 一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_sort_table',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
], false);
?>

<?php
$this->BcBaser->i18nScript([
	'message1' => __d('baser', "このデータを本当に無効にしてもいいですか？\nプラグインフォルダ内のファイル、データベースに保存した情報は削除されずそのまま残ります。"),
	'message2' => __d('baser', "本当に削除してもいいですか？\nプラグインフォルダ内のファイル、データベースのデータも全て削除されます。"),
	'message3' => __d('baser', "本当に無効にしてもいいですか？\nプラグインフォルダ内のファイル、データベースに保存した情報は削除されずそのまま残ります。")
]);
?>

<script type="text/javascript">
	$(function () {
		// データリスト設定
		$.baserAjaxDataList.config.methods.del.confirm = bcI18n.message1;
		$.baserAjaxDataList.config.methods.del.result = null;
		$.baserAjaxDataList.config.methods.delfile = {
			button: '.btn-delfile',
			confirm: bcI18n.message2
		}
		// 一括処理設定
		$.baserAjaxBatch.config.methods.del.confirm = bcI18n.message3;
		$.baserAjaxBatch.config.methods.del.result = null;
		$.baserAjaxDataList.init();
		$.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
		$.baserAjaxSortTable.init({url: $("#AjaxSorttableUrl").html()});

		/**
		 * マーケットのデータを取得
		 */
		$.ajax({
			url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/plugins/ajax_get_market_plugins',
			type: "GET",
			success: function (result) {
				$("#BaserMarket").html(result);
			}
		});

		$("#tabs").tabs();

	});
</script>


<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'plugins', 'action' => 'ajax_batch']) ?></div>
<div id="AjaxSorttableUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'plugins', 'action' => 'ajax_update_sort']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>

<div id="tabs">
	<ul>
		<li><a href="#DataList"><?php echo __d('baser', '所有プラグイン') ?></a></li>
		<li><a href="#BaserMarket"><?php echo __d('baser', 'baserマーケット') ?></a></li>
	</ul>
	<div id="DataList"><?php $this->BcBaser->element('plugins/index_list') ?></div>
	<div id="BaserMarket">
		<div
			style="padding:20px;text-align:center;"><?php $this->BcBaser->img('admin/ajax-loader.gif', ['alt' => 'Loading...']) ?></div>
	</div>
</div>
