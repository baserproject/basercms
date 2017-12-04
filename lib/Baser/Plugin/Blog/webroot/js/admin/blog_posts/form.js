$(function(){

	var fullUrl = $("#AdminBlogBLogPostsEditScript").attr('data-fullurl');
	var previewurlBase = $("#AdminBlogBLogPostsEditScript").attr('data-previewurl');

	$("input[type=text]").each(function(){
		$(this).keypress(function(e){
			if(e.which && e.which === 13) {
				return false;
			}
			return true;
		});
	});

	if (!document.queryCommandSupported('copy')) {
		$("#BtnCopyUrl").hide();
	}
	$("#BtnCopyUrl").click(function(){
		var copyArea = $("<textarea style=\" opacity:0; width:1px; height:1px; margin:0; padding:0; border-style: none;\"/>");
		copyArea.text(fullUrl);
		$(this).after(copyArea);
		copyArea.select();
		document.execCommand("copy");
		copyArea.remove();
		return false;
	});

	$("#BtnPreview").click(function(){
		window.open('', 'preview');
		var form = $(this).parents('form');
		var action = form.attr('action');
		var previewMode = 'default';
		var previewurl = previewurlBase;

		if ($("#DraftModeDetailTmp").val() == 'draft') {
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
	
	/**
	 * フォーム送信時イベント
	 */
	$("#BtnSave").click(function(){
		$.bcUtil.showLoader();
		if(typeof editor_detail_tmp != "undefined") {
			editor_detail_tmp.execCommand('synchronize');
		}
		$("#BlogPostMode").val('save');
		$.bcToken.check(function(){
			$("#BlogPostForm").submit();
		}, {useUpdate: false, hideLoader: false});
		return false;
	});
	
	/**
	 * ブログタグ追加
	 */
	$("#BlogTagName").keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$("#BtnAddBlogTag").click();
			return false;
		} else {
			return true;
		}
	});
	
	$("#BtnAddBlogTag").click(function(){
		if(!$("#BlogTagName").val()) {
			return false;
		}
		$.bcToken.check(function(){
			return $.ajax({
				type: "POST",
				url: $("#AddTagUrl").html(),
				data: {
					'data[BlogTag][name]': $("#BlogTagName").val(),
					'data[_Token][key]': $.bcToken.key
				},
				dataType: 'html',
				beforeSend: function() {
					$("#BtnAddBlogTag").prop('disabled', true);
					$("#TagLoader").show();
				},
				success: function(result){
					if(result) {
						$("#BlogTags").append(result);
						$('input[name="data[BlogTag][BlogTag][]"]').last().prop('checked', true);
						$("#BlogTagName").val('');
					} else {
						alert('ブログタグの追加に失敗しました。既に登録されていないか確認してください。');
					}
				},
				error: function(){
					alert('ブログタグの追加に失敗しました。');
				},
				complete: function(xhr, textStatus) {
					$("#BtnAddBlogTag").removeAttr('disabled');
					$("#TagLoader").hide();
					$("#BlogTags").effect("highlight",{},1500);
				}
			});
		}, {loaderType: 'target', loaderSelector: '#TagLoader', hideLoader: false});
		return false;
	});
	
	/**
	 * ブログカテゴリ追加
	 */
	$("#BtnAddBlogCategory").click(function(){
		$("#AddBlogCategoryForm").dialog({
			height: 250,
			width: 600,
			open: function() {
				// ヘルプがダイアログ内で切れてしまうのを防ぐ
				$(this).css('overflow', 'visible');
				$(this.parentNode).css('overflow', 'visible');
			}
		});
		return false;
	});

	$("#BtnBlogCategoryCancel").click(function(ev) {

		$("#AddBlogCategoryForm").dialog('close');
		return false;
	});

	$("#BtnBlogCategorySave").click(function() {

		var name = $('#AddBlogCategoryForm [name="data[BlogCategory][name]"]').val();
		var title = $('#AddBlogCategoryForm [name="data[BlogCategory][title]"]').val();

		if(!name) {
			name = title;
		}

		$.bcToken.check(function(){
			return $.ajax({
				type: "POST",
				url: $("#AddBlogCategoryUrl").html(),
				data: {
					'data[BlogCategory][name]': name,
					'data[BlogCategory][title]': title,
					'data[_Token][key]': $.bcToken.key
				},
				dataType: 'script',
				beforeSend: function() {
					$("#BtnAddBlogCategory").prop('disabled', true);
					$.bcUtil.showLoader();
				},
				success: function(result){
					if(result) {
						$("#BlogPostBlogCategoryId").append($('<option />').val(result).html(title));
						$("#BlogPostBlogCategoryId").val(result);
					} else {
						alert('ブログカテゴリの追加に失敗しました。入力したブログカテゴリ名が既に登録されていないか確認してください。');
					}
					$("#AddBlogCategoryForm").dialog('close');
				},
				error: function(XMLHttpRequest, textStatus){
					if(XMLHttpRequest.responseText) {
						alert('ブログカテゴリの追加に失敗しました。\n\n' + XMLHttpRequest.responseText);
					} else {
						alert('ブログカテゴリの追加に失敗しました。\n\n' + XMLHttpRequest.statusText);
					}
				},
				complete: function(xhr, textStatus) {
					$("#BtnAddBlogCategory").removeAttr('disabled');
					$.bcUtil.hideLoader();
					$("#BlogPostBlogCategoryId").effect("highlight",{},1500);
				}
			});
		}, {hideLoader: false});
		return false;
	});
});
