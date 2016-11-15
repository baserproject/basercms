$(function(){
	$("#EditBlogTemplate").click(function(){
		if(confirm('ブログ設定を保存して、コンテンツテンプレート '+$("#BlogContentTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#BlogContentEditLayoutTemplate").val('');
			$("#BlogContentEditBlogTemplate").val(1);
			$("#BlogContentAdminEditForm").submit();
		}
	});
});
