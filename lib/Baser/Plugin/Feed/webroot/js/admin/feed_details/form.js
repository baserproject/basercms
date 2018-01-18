$(function(){
	$("#BtnSave").click(function(){
		$.bcUtil.showLoader();
	});
	$("#EditTemplate").click(function(){
		if(confirm('フィード設定を保存して、テンプレート '+$("#FeedConfigTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#FeedConfigEditTemplate").val(true);
			$("#FeedConfigAdminEditForm").submit();
		}
	});
});