/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サイト編集
 */

$(function(){
    $("#BtnDelete").click(function(){
		if(confirm('サブサイトを削除してもよろしいですか？\nサブサイトに関連しているコンテンツは全てゴミ箱に入ります。')) {
			var form = $(this).parents('form');
			form.attr('action', $.baseUrl + '/admin/sites/delete');
			form.submit();
		}
        return false;
    });
});