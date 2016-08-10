$(function(){

	$("input[type=text]").each(function(){
		$(this).keypress(function(e){
			if(e.which && e.which === 13) {
				return false;
			}
			return true;
		});
	});

	/**
	 * プレビューボタンクリック時イベント
	 */
	var useContent = Number($("#UseContent").val());
	$("#BtnPreview").click(function(){

		var detail = $("#BlogPostDetail").val();
		if(typeof editor_detail_tmp != "undefined") {
			$("#BlogPostDetail").val(editor_detail_tmp.getData());
		}

		$.ajax({
			type: "POST",
			url: $("#CreatePreviewUrl").html(),
			data: $("#BlogPostForm").serialize(),
			success: function(result){
				if(result) {
					$("#LinkPreview").trigger("click");
				} else {
					alert('プレビューの読み込みに失敗しました。');
				}
			}
		});

		$("#BlogPostDetail").val(detail);

		return false;

	});

	$("#LinkPreview").colorbox({width:"90%", height:"90%", iframe:true});
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
				$("#BtnAddBlogTag").attr('disabled', 'disabled');
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
				$("#BtnAddBlogCategory").attr('disabled', 'disabled');
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
