<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->js(array(
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
					$.baserAjaxDataList.load(document.location.href);
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
					$.baserAjaxDataList.load(document.location.href);
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
				'※ プラグイン管理情報、管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。')) {
				return true;
			}
			return false;
		});
	});
</script>


<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'themes', 'action' => 'ajax_batch')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $bcBaser->element('themes/index_list') ?></div>

