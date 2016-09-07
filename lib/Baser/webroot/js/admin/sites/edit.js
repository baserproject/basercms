/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サイト編集
 */

$(function(){
    $("#BtnDelete").click(function(){
		if(confirm('サブサイトを削除してもよろしいですか？\nサブサイトに関連しているコンテンツは全てゴミ箱に入ります。')) {
			var form = $(this).parents('form');
			form.attr('action', $.baseUrl + '/admin/sites/delete');
			form.submit();
		}
        return false;
    });

	$("#SiteDevice, #SiteLang").change(loadView);
	$('input[name="data[Site][same_main_url]"]').click(loadView);
	
	loadView();
	
	function loadView() {
		var autoRedirect = $("#SiteAutoRedirect");
		var sameMainUrl = $("#SiteSameMainUrl");
		var autoLink = $("#SiteAutoLink");
		if($("#SiteDevice").val() || $("#SiteLang").val()) {
			$("#SectionAccessType").show();
		} else {
			$("#SectionAccessType").hide();
			autoRedirect.prop('checked', false);
			sameMainUrl.prop('checked', false);
			autoLink.prop('checked', false);
		}
		if(sameMainUrl.prop('checked')) {
			autoRedirect.prop('checked', false);
			autoRedirect.prop('disabled', true);
			autoLink.prop('checked', false);
			autoLink.prop('disabled', true);
		} else {
			autoRedirect.prop('disabled', false);
			if($("#SiteDevice").val() == 'mobile' || $("#SiteDevice").val() == 'smartphone') {
				autoLink.prop('disabled', false);	
			}
		}
	}
});