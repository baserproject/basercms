/* SVN FILE: $Id$ */
/**
 * 共通スタートアップ処理
 * 
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$(document).ready(function(){
	$('.blog-widget h2').click(function() {
		if($(this).next().css('display') == 'block') {
			return false;
		}
		$(this).parent().parent().find('ul').hide();
		$(this).next().show();
		$(this).parent().parent().find('h2').css('background-position', '93% top');
		$(this).css('background-position', '93% -34px');
		return false;
	}).parent().next().find('ul').hide();
	$('.blog-widget h2:first').css('background-position', '93% -34px');
	$('#ToTop a').click(function(){
            $(this).blur();
            $('html,body').animate({ scrollTop: 0 }, 'fast');
            return false;
	});
});