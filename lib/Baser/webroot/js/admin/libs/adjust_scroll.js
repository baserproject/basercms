/**
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * 高さを調整する
 */

$(function () {

    var adminPage = function () {
        if ($('html').css('margin-top') != '0px') {
            var h = $(window).height() - $("#ToolBar").outerHeight();
        } else {
            var h = $(window).height();
        }

        var fh = $('#Footer').height();
        var lh = $('#Login').height();

        if ($('#Login').length && !lh) {
            // ログイン画面のロード実行時は高さが取れない
            setTimeout(adminPage, 50);
        } else {
            if (lh && lh > h - fh) {
                h = lh + fh;
            }
            $('#Page').css('min-height', h);
        }
    };
    adminPage();
    $(window).resize(adminPage);
    $(window).scroll(adminPage);

});
