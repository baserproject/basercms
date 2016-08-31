$(function(){

	var fullUrl = $("#AdminBlogBLogPostsEditScript").attr('data-fullurl');

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
    	var copyArea = $("<textarea/>");
    	copyArea.text(fullUrl);
    	$("body").append(copyArea);
		copyArea.select();
    	document.execCommand("copy");
    	copyArea.remove();
    	return false;
	});

    $("#BtnPreview").click(function(){
        window.open('', 'preview');
        var form = $(this).parents('form');
        var action = form.attr('action');
        form.attr('target', 'preview');
        form.attr('action', $.baseUrl + fullUrl + '?preview=default');
        form.submit();
        form.attr('target', '_self');
        form.attr('action', action);
        $.get($.baseUrl + '/admin/contents/ajax_get_token', function(result) {
            $('input[name="data[_Token][key]"]').val(result);
        });
        return false;
    });
	
	/**
	 * フォーム送信時イベント
	 */
	$("#BtnSave").click(function(){
		if(typeof editor_detail_tmp != "undefined") {
			editor_detail_tmp.execCommand('synchronize');
		}
		$("#BlogPostMode").val('save');
		$("#BlogPostForm").submit();
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
		$.ajax({
			type: "POST",
			url: $("#AddTagUrl").html(),
			data: {'data[BlogTag][name]': $("#BlogTagName").val()},
			dataType: 'html',
			beforeSend: function() {
				$("#BtnAddBlogTag").prop('disabled', true);
				$("#TagLoader").show();
			},
			success: function(result){
				if(result) {
					$("#BlogTags").append(result);
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
		return false;
	});
	
	/**
	 * ブログカテゴリ追加
	 */
	$("#BtnAddBlogCategory").click(function(){
		var category = prompt("新しいブログカテゴリを入力してください。");
		if(!category) {
			return false;
		}
		$.ajax({
			type: "POST",
			url: $("#AddBlogCategoryUrl").html(),
			data: {'data[BlogCategory][name]': category},
			dataType: 'script',
			beforeSend: function() {
				$("#BtnAddBlogCategory").prop('disabled', true);
				$("#BlogCategoryLoader").show();
			},
			success: function(result){
				if(result) {
					$("#BlogPostBlogCategoryId").append($('<option />').val(result).html(category));
					$("#BlogPostBlogCategoryId").val(result);
				} else {
					alert('ブログカテゴリの追加に失敗しました。既に登録されていないか確認してください。');
				}
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
				$("#BlogCategoryLoader").hide();
				$("#BlogPostBlogCategoryId").effect("highlight",{},1500);
			}
		});
		return false;
	});
});
