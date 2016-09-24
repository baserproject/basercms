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

	$("#SiteMainSiteId").change(loadDeviceAndLang);
	$("#SiteDevice, #SiteLang").change(loadOptions);
	$('input[name="data[Site][same_main_url]"]').click(loadOptions);
	
	loadDeviceAndLang();

/**
 * デバイスと言語の表示設定
 */
	function loadDeviceAndLang() {
		$.bcUtil.ajax($.baseUrl + '/admin/sites/ajax_get_selectable_devices_and_lang/' + $("#SiteMainSiteId").val(), function(result){
			var selectDevice = $("#SiteDevice");
			var selectLang = $("#SiteLang");
			var device = selectDevice.val();
			var lang = selectLang.val();
			selectDevice.find('option').remove();
			selectLang.find('option').remove();
			result = $.parseJSON(result);
			$.each(result.devices, function (value, name) {
				selectDevice.append($('<option>').val(value).text(name).prop('selected', (value === device)));
			});
			$.each(result.langs, function (value, name) {
				selectLang.append($('<option>').val(value).text(name).prop('selected', (name === lang)));
			});
			loadView();
		}, {type: 'GET', loaderType: 'after', loaderSelector: '#SiteMainSiteId'});
	}
	
/**
 * デバイスと言語のオプションの表示設定 
 */
	function loadOptions() {
		var autoRedirect = $("#SiteAutoRedirect");
		var sameMainUrl = $("#SiteSameMainUrl");
		var autoLink = $("#SiteAutoLink");
		var spanAutoRedirect = $("#SpanSiteAutoRedirect");
		var spanAutoLink = $("#SpanSiteAutoLink");
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
			spanAutoRedirect.hide();
			autoLink.prop('checked', false);
			spanAutoLink.hide();
		} else {
			spanAutoRedirect.show();
			if($("#SiteDevice").val() == 'mobile' || $("#SiteDevice").val() == 'smartphone') {
				spanAutoLink.show();
			} else {
				spanAutoLink.hide();
			}
		}
	}
	
});