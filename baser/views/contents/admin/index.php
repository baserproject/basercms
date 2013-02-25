<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 検索インデックス一覧
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
	'admin/jquery.baser_ajax_batch', 
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
?>
<script type="text/javascript">
$(function(){
	if($("#ContentOpen").html()) {
		$("#ContentFilterBody").show();
	}
	$(".priority").change(function() {
		var id = this.id.replace('ContentPriority', '');
		var data = {
			'data[Content][id]':id,
			'data[Content][priority]':$(this).val()
		};
		$.ajax({
			type: "POST",
			url: $("#AjaxChangePriorityUrl").html()+'/'+id,
			data: data,
			beforeSend: function() {
				$("#flashMessage").slideUp();
				$("#PriorityAjaxLoader"+id).show();
			},
			success: function(result){
				if(!result) {
					$("#flashMessage").html('処理中にエラーが発生しました。');
					$("#flashMessage").slideDown();
				}
			},
			error: function() {
				$("#flashMessage").html('処理中にエラーが発生しました。');
				$("#flashMessage").slideDown();
			},
			complete: function() {
				$("#PriorityAjaxLoader"+id).hide();
			}
		});
	});
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});

});

</script>

<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'contents', 'action' => 'ajax_batch')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="AjaxChangePriorityUrl" class="display-none"><?php echo $bcBaser->url(array('action' => 'ajax_change_priority')) ?></div>
<div id="ContentOpen" class="display-none"><?php echo $bcForm->value('Content.open') ?></div>
<div id="DataList"><?php $bcBaser->element('contents/index_list') ?></div>