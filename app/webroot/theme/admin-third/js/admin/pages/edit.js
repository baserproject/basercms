/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


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
		$.bcToken.check(function(){
			$("#PageAdminEditForm").submit();
		}, {useUpdate: false, hideLoader: false});
		return false;
	});
});
