$(function(){
	$("#BtnUpdate").click(function(){
		if(confirm('アップデートを実行します。よろしいですか？')) {
			$.bcUtil.showLoader();
			return true;
		}
		return false;
	});
});
