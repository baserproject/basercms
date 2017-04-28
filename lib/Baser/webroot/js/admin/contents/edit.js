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
 * コンテンツ編集
 */

$(function(){
	window.setTimeout(function() {
		window.scrollTo(0, 1);
	}, 100);
	var fullUrl = $("#AdminContentsEditScript").attr('data-fullurl');
	var previewurl = $("#AdminContentsEditScript").attr('data-previewurl');
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
		if (window.editor_contents_tmp && editor_contents_tmp.draftMode && editor_contents_tmp.draftMode == 'draft') {
			previewMode = 'draft';
		}
		if (previewurl.match(/\?/)) {
			previewurl += '&preview=' + previewMode;
		} else {
			previewurl += '?preview=' + previewMode;
		}

        form.attr('target', 'preview');
        form.attr('action', previewurl);
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get($.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/ajax_get_token', function(result) {
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
			form.attr('action', $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/delete');
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
			$.bcToken.check(function(){
				$.ajax({
					url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/exists_content_by_url',
					type: 'POST',
					data: {
						data: {url: targetUrl},
						_Token: {
							key: $.bcToken.key
						}
					},
					beforeSend: function () {
						$.bcUtil.showLoader();
					},
					success: function (result) {
						if(!result) {
							$.bcToken.check(function(){
								$.ajax({
									url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/add/1',
									type: 'POST',
									data: $.extend(data, {
										_Token: {
											key: $.bcToken.key
										}
									}),
									dataType: 'json',
									beforeSend: function () {
										$("#Waiting").show();
									},
									success: function (result) {
										$.bcUtil.showNoticeMessage('エイリアスを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。')
										location.href = $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/edit_alias/' + result.id;
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										$.bcUtil.hideLoader();
										$.bcUtil.showAlertMessage('エイリアスの作成に失敗しました。');
									}
								});
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
			$.bcToken.check(function(){
				$.ajax({
					url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/contents/exists_content_by_url',
					type: 'POST',
					data: {
						data: {url: targetUrl},
						_Token: {
							key: $.bcToken.key
						}
					},
					beforeSend: function () {
						$.bcUtil.showLoader();
					},
					success: function (result) {
						if(!result) {
							$.bcToken.check(function(){
								$.ajax({
									url: bcManageContent[current.Content.type]['url']['copy'],
									type: 'POST',
									data: $.extend(data, {
										_Token: {
											key: $.bcToken.key
										}
									}),
									dataType: 'json',
									beforeSend: function () {
										$("#Waiting").show();
									},
									success: function (result) {
										$.bcUtil.showNoticeMessage('コピーを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。')
										location.href = bcManageContent[current.Content.type]['url']['edit'] + '/' + result.entity_id;
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										$.bcUtil.hideLoader();
										$.bcUtil.showAlertMessage('コピーの作成に失敗しました。');
									}
								});
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
			});
		}
		return false;
    });

	if(!$("#ContentModifiedDate").val()) {
		$("#ContentModifiedDateDate").val(getNowDate());
		$("#ContentModifiedDateTime").val(getNowTime());
	}

});