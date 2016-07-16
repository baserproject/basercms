/**
 * コンテンツ一覧
 *
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2016, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2016, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


$(function(){
	var mode  = $("#ViewSettingMode").val();
	var url;
	if(mode == 'index') {
		var siteId = $("input[name='data[ViewSetting][site_id]']:checked").val();
		if(siteId == undefined) {
			siteId = 'all';
		}
		url = $.baseUrl+'/admin/contents/index/site_id:' + siteId;
	} else if(mode == 'trash') {
		url = $.baseUrl+'/admin/contents/trash_index';
	}
	$.ajax({
		type: "POST",
		url: url,
		beforeSend: function() {
			$.bcUtil.showLoader();
		},
		success: function(result){
			if(result) {
				$("#DataList").html(result);
			}
		},
		complete: function() {
			$.bcUtil.hideLoader();
		}
	});
});