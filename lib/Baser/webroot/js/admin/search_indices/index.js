/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

$(function(){
	$("#SearchIndexSiteId").change(function(){
		$.ajax({
			url: $.baseUrl + '/admin/contents/ajax_get_content_folder_list/' + $(this).val(),
			type: "GET",
			dataType: "json",
			beforeSend: function(){
				$("#SearchIndexSiteIdLoader").show();
				$("#SearchIndexFolderId").prop('disabled', true);
			},
			complete: function(){
				$("#SearchIndexFolderId").removeAttr("disabled");
				$("#SearchIndexSiteIdLoader").hide();
			},
			success: function(result){
				$("#SearchIndexFolderId").empty();
				var optionItems = [];
				optionItems.push(new Option("指定なし", ""));
				for (key in result) {
					optionItems.push(new Option(result[key].replace(/&nbsp;/g,"\u00a0"), key));
				}
				$("#SearchIndexFolderId").append(optionItems);
			}
		});
	});
	if($("#SearchIndexOpen").html()) {
		$("#SearchIndexFilterBody").show();
	}
	$(".priority").change(function() {
		var id = this.id.replace('SearchIndexPriority', '');
		var priority = $(this).val();
		$.bcToken.check(function(){
			var data = {
				'data[SearchIndex][id]':id,
				'data[SearchIndex][priority]': priority,
				'data[_Token][key]': $.bcToken.key
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
	});
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});