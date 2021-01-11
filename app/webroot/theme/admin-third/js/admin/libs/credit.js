/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * クレジット表示処理
 */

$(function () {
    $("#BtnCredit").click(credit);
});

/**
 * クレジットを表示する
 */
function credit() {

    $.ajax({
        url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/site_configs/ajax_credit',
        type: "GET",
        success: function (result) {
            var hideTarget;
            var isLogin = false;
            if ($("#SideBar").css('display') == 'none') {
                openedFavorite = false;
                hideTarget = "#Contents";
            } else {
                openedFavorite = true;
                hideTarget = "#Contents, #SideBar";
            }
            if ($("#Credit").size()) {
                isLogin = true;
                $("#Credit").remove();
            }

            if ($("html").css('margin-top') != '0px') {
                $("html").prepend(result);
            } else {
                $("#Page").prepend(result);
            }

            if (_ua.ltIE8) {
                if ($("html").css('margin-top') != '0px') {
                    $("html").prepend(result);
                } else {
                    $("#Page").prepend(result);
                }
                $("#Credit").show();
                $("#Page").css('overflow', 'hidden');
                $("#Footer").hide();
                $(hideTarget).hide(0, function () {
                    $("#Footer").show();
                    setViewSize();
                });
                $("#CreditScroller").show();
            } else {
                if (isLogin) {
                    $("#Credit").show();
                } else {
                    $("#Credit").fadeIn(1000);
                }
                $("#Page").css('overflow', 'hidden');
                if (!isLogin) {
                    $("#Footer").fadeOut(500);
                    $(hideTarget).fadeOut(500, function () {
                        $("#Footer").fadeIn(2000);
                        setViewSize();
                    });
                }
                $("#CreditScroller").fadeIn(1000);
            }

            //リサイズイベント
            $(window).resize(function () {
                resizeScroll();
            });

            var scrollSpeed = 1;
            var height = $("#CreditScroller").height();
            var posX = $(window).height();
            var id = setInterval(function () {
                if (posX < -height + $(window).height() / 2) {
                    /*posX= $(window).height();*/
                    clearInterval(id);
                }
                posX -= scrollSpeed;
                $('#CreditScroller').css("margin-top", posX + "px");
            }, 40);

            $("#Credit").click(function () {
                clearTimeout(id);
                $("#Credit").fadeOut(1000, function () {
                    $("#Credit").remove();
                });
                if ($('#Login').length > 0) {
                    hideTarget = "";
                    $("#Wrap").css('height', '280px');
                    $("#LoginInner").css('color', '#333');
                } else {
                    $("#Wrap").css('height', 'auto');
                    if (!openedFavorite) {
                        hideTarget = "#Contents";
                    } else {
                        hideTarget = "#Contents, #SideBar";
                    }
                    $(hideTarget).fadeIn(1000);
                }
                $("#Page").css('height', 'auto');
                $("#Page").css('overflow', 'auto');
            });

            $("#CreditScrollerInner").click(function (e) {
                if (e && e.stopPropagation) {
                    e.stopPropagation();
                } else {
                    window.event.cancelBubble = true;
                }
            });

        }
    });
}

/**
 * 表示領域を設定
 */
function setViewSize() {
    $("#Wrap").css('height', '280px');
    $("html").height($(this).height() - $("#ToolBar").outerHeight() * 1);
    $("#Credit").height($("#Page").height() + $("#ToolBar").outerHeight() * 1);
    $("#Credit").width($("#Page").width());
}

/**
 * スクロールバーを非表示に
 */
function resizeScroll() {
    $("html,body").height($(this).height() - $("#ToolBar").outerHeight() * 1);
    $("#Credit").width($("#Page").width());
    $("#Credit").height($("#Page").height() + $("#ToolBar").outerHeight() * 1);
}
