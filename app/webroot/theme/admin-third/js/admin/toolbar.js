/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

$(function () {
    $('#UserMenu').fixedMenu();
    $('#SystemMenu h2').click(function () {
        if ($(this).next().css('display') == 'none') {
            $(this).next().slideDown(200);
        } else {
            $(this).next().slideUp(200);
        }
    });
    $('#SystemMenu ul:first').show();
    $("#UserMenu ul li div ul li").each(function () {
        if (!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
            $(this).remove();
        }
    });
    $("#UserMenu ul li div ul").each(function () {
        if (!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
            $(this).prev().remove();
            $(this).remove();
        }
    });
});
