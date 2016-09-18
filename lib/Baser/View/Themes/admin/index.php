<?php
/**
 * [ADMIN] テーマ一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/baser_ajax_data_list_config'
));
?>


<script type="text/javascript">
$(function(){
	$.baserAjaxDataList.config.methods.copy = {
		button: '.btn-copy',
		confirm: '',
		result: function(row, result) {
			console.log(result);
			var config = $.baserAjaxDataList.config;
			if(result) {
				$.bcUtil.showLoader();
				document.location.reload();
			} else {
				$(config.alertBox).html('コピーに失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	}
	$.baserAjaxDataList.config.methods.del = {
		button: '.btn-delete',
		confirm: 'このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				$.bcUtil.showLoader();
				document.location.reload();
			} else {
				$(config.alertBox).html('削除に失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	}
	$.baserAjaxDataList.init();
	$("#BtnLoadDefaultDataPattern").click(function() {
		if(confirm(
			'初期データを読み込みます。よろしいですか？\n\n'+
			'※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。\n'+
			'※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。')) {
			return true;
		}
		return false;
	});
	
/**
 * マーケットのデータを取得
 */
	$.ajax({
		url: $.baseUrl + '/admin/themes/ajax_get_market_themes',
		type: "GET",
		success: function(result) {
			$("#BaserMarket").html(result);
		}
	});

	$( "#tabs" ).tabs();

});
</script>
  
<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'themes', 'action' => 'ajax_batch')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>

<div id="tabs">
	<ul>
		<li><a href="#DataList">所有テーマ</a></li>
		<li><a href="#BaserMarket">baserマーケット</a></li>
	</ul>
	<div id="DataList"><?php $this->BcBaser->element('themes/index_list') ?></div>
	<div id="BaserMarket"><div style="padding:20px;text-align:center;"><?php $this->BcBaser->img('admin/ajax-loader.gif', array('alt' => 'Loading...')) ?></div></div>
</div>
