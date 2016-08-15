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
	var disabledColor;
	$("#SearchIndexSiteId").change(function(){
		$.ajax({
			url: $.baseUrl + '/admin/search_indices/ajax_get_content_folder_list/' + $(this).val(),
			type: "GET",
			dataType: "json",
			beforeSend: function(){
				$("#SearchIndexSiteIdLoader").show();
				$("#SearchIndexFolderId").attr('disabled', "disabled");
				disabledColor = $("#SearchIndexFolderId").css('color');
				$("#SearchIndexFolderId").css('color', '#CCC');
			},
			complete: function(){
				$("#SearchIndexFolderId").removeAttr("disabled");
				$("#SearchIndexFolderId").css('color', disabledColor);
				$("#SearchIndexSiteIdLoader").hide();
			},
			success: function(result){
				$("#SearchIndexFolderId").empty();
				var optionItems = [];
				optionItems.push(new Option("指定なし", ""));
				for (key in result) {
					optionItems.push(new Option(result[key], key));
				}
				$("#SearchIndexFolderId").append(optionItems);
			}
		});
	});
});