/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */


$(function(){
	$('#UserMenu').fixedMenu();
	$("#UserMenu ul li div ul li").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).remove();
		}
	});
	$("#UserMenu ul li div ul").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).prev().remove();
			$(this).remove();
		}
	});
	$("#BtnLogout").click(function(){
	    $.bcJwt.logout();
	});
});
