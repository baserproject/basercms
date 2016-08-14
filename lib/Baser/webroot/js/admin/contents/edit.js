/**
 * コンテンツ編集
 *
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


$(function(){
	window.setTimeout(function() {
		window.scrollTo(0, 1);
	}, 100);
	var fullUrl = $("#AdminContentsEditScript").attr('data-fullurl');
	var current = $.parseJSON($("#AdminContentsEditScript").attr('data-current'));
	var bcManageContent = $.parseJSON($("#AdminContentsEditScript").attr('data-settings'));

    $("form #ContentsFormTabs").tabs().show();

    $("#BtnPreview").click(function(){
        window.open('', 'preview');
        var form = $(this).parents('form');
        var action = form.attr('action');
		var previewMode = 'default';

		if($("#ContentAliasId").val()) {
			previewMode = 'alias';
		}
        form.attr('target', 'preview');
        form.attr('action', $.baseUrl + fullUrl + '?preview=' + previewMode);
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get($.baseUrl + '/admin/contents/ajax_get_token', function(result) {
            $('input[name="data[_Token][key]"]').val(result);
        });
        return false;
    });

    $("#BtnDelete").click(function(){
    	var message = 'コンテンツをゴミ箱に移動してもよろしいですか？';
    	if($("#ContentAliasId").val()) {
    		message = 'エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。';
    	}
		if(confirm(message)) {
			var form = $(this).parents('form');
			form.attr('action', $.baseUrl + '/admin/contents/delete');
			form.submit();
		}
        return false;
    });

	$("#BtnCopyUrl").click(function(){
    	var copyArea = $("<textarea/>");
    	copyArea.text(fullUrl);
    	$("body").append(copyArea);
		copyArea.select();
    	document.execCommand("copy");
    	copyArea.remove();
    	return false;
	});

    $(".create-alias").click(function(){
    	var siteId = $(this).attr('data-site-id');
    	var displayName = $("#SiteDisplayName" + siteId).val();
    	var targetUrl = $("#SiteTargetUrl" + siteId).val();
    	var data = {'Content' : {
			title: current.Content.name,
			plugin: current.Content.plugin,
			type: current.Content.type,
			site_id: siteId,
			alias_id: current.Content.id,
			entity_id: current.Content.entity_id,
			url: current.Content.url
    	}};
		if(confirm('このコンテンツを元に ' + displayName + 'にエイリアスを作成します。よろしいですか？')) {

			$.ajax({
				url: $.baseUrl + '/admin/contents/exists_content_by_url',
				type: 'POST',
				data: {data: {url: targetUrl}},
				beforeSend: function () {
					$.bcUtil.showLoader();
				},
				success: function (result) {
					if(!result) {
						$.ajax({
							url: $.baseUrl + '/admin/contents/add/1',
							type: 'POST',
							data: data,
							dataType: 'json',
							beforeSend: function () {
								$("#Waiting").show();
							},
							success: function (result) {
								$.bcUtil.showNoticeMessage('エイリアスを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。')
								location.href = $.baseUrl + '/admin/contents/edit_alias/' + result.contentId;
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								$.bcUtil.hideLoader();
								$.bcUtil.showAlertMessage('エイリアスの作成に失敗しました。');
							}
						});
					} else {
						$.bcUtil.hideLoader();
						$.bcUtil.showAlertMessage('指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。エイリアスの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。');
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.hideLoader();
					$.bcUtil.showAlertMessage('エイリアスの作成に失敗しました。');
				}
			});
		}
		return false;
    });

   $(".create-copy").click(function(){
    	var siteId = $(this).attr('data-site-id');
    	var displayName = $("#SiteDisplayName" + siteId).val();
    	var targetUrl = $("#SiteTargetUrl" + siteId).val();
    	var data = {
			title: current.Content.title,
			siteId: siteId,
			parentId: current.Content.parent_id,
			contentId: current.Content.id,
			entityId: current.Content.entity_id,
			url: current.Content.url
    	};
		if(confirm('このコンテンツを元に ' + displayName + 'にコピーを作成します。よろしいですか？')) {
			$.ajax({
				url: $.baseUrl + '/admin/contents/exists_content_by_url',
				type: 'POST',
				data: {data: {url: targetUrl}},
				beforeSend: function () {
					$.bcUtil.showLoader();
				},
				success: function (result) {
					if(!result) {
						$.ajax({
							url: bcManageContent[current.Content.type]['routes']['copy'],
							type: 'POST',
							data: data,
							dataType: 'json',
							beforeSend: function () {
								$("#Waiting").show();
							},
							success: function (result) {
								$.bcUtil.showNoticeMessage('コピーを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。')
								location.href = bcManageContent[current.Content.type]['routes']['edit'] + '/' + result.entityId;
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								$.bcUtil.hideLoader();
								$.bcUtil.showAlertMessage('コピーの作成に失敗しました。');
							}
						});
					} else {
						$.bcUtil.hideLoader();
						$.bcUtil.showAlertMessage('指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。コピーの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。');
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$.bcUtil.hideLoader();
					$.bcUtil.showAlertMessage('コピーの作成に失敗しました。');
				}
			});
		}
		return false;
    });

	// TODO もっといい書き方を・・・ToT
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var day = date.getDate();
	var hour = date.getHours();
	var minute = date.getMinutes();
	if ( month < 10 ) month = '0' + month;
	if ( day < 10 ) day = '0' + day;
	if ( hour < 10 ) hour = '0' + hour;
	if ( minute < 10 ) minute = '0' + minute;
	$("#ContentModifiedDateDate").val(year + '/' + month + '/' + day);
	$("#ContentModifiedDateTime").val(hour + ':' + minute);

});