$(window).load(function() {
	$("#PageName").focus();
});
$(function(){
	$("input[type=text]").each(function(){
		$(this).keypress(function(e){
			if(e.which && e.which === 13) {
				return false;
			}
			return true;
		});
	});
	$("#BtnSave").click(function(){
		if(typeof editor_contents_tmp != "undefined") {
			editor_contents_tmp.execCommand('synchronize');
		}
		$("#PageMode").val('save');
		$("#PageAdminEditForm").submit();
		return false;
	});
});