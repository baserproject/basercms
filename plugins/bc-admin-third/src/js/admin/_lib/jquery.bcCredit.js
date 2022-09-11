/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * bcCredit
 */

(function ($) {
    $.bcCredit = {
        show: function () {
            $.ajax({
                url: $.bcUtil.adminBaseUrl + 'baser-core/utilities/credit',
                type: "GET",
                success: function (result) {
                    let hideTarget;
                    let isLogin = false;
                    let $html = $("html");
                    let $page = $("#Page");
                    let $credit = $("#Credit");
                    if ($("#SideBar").css('display') === 'none') {
                        openedFavorite = false;
                        hideTarget = "#Contents";
                    } else {
                        openedFavorite = true;
                        hideTarget = "#Contents, #SideBar";
                    }
                    if ($credit.length) {
                        isLogin = true;
                        $credit.remove();
                    }

                    if ($html.css('margin-top') !== '0px') {
                        $html.prepend(result);
                    } else {
                        $page.prepend(result);
                    }
                    // 再度読み込み直す
                    $credit = $("#Credit");
                    let $creditScroller = $("#CreditScroller");

                    if (isLogin) {
                        $credit.show();
                    } else {
                        $credit.fadeIn(1000);
                    }
                    $page.css('overflow', 'hidden');
                    if (!isLogin) {
                        $("#Footer").fadeOut(500);
                        $(hideTarget).fadeOut(500, function () {
                            $("#Footer").fadeIn(2000);
                            $.bcCredit.setViewSize();
                        });
                    }
                    $creditScroller.fadeIn(1000);

                    //リサイズイベント
                    $(window).resize(function () {
                        $.bcCredit.resizeScroll();
                    });

                    var scrollSpeed = 1;
                    var height = $creditScroller.height();
                    var posX = $(window).height();
                    var id = setInterval(function () {
                        if (posX < -height + $(window).height() / 2) {
                            clearInterval(id);
                        }
                        posX -= scrollSpeed;
                        $creditScroller.css("margin-top", posX + "px");
                    }, 40);

                    $credit.click(function () {
                        clearTimeout(id);
                        $credit.fadeOut(1000, function () {
                            $credit.remove();
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
                        $page.css('height', 'auto').css('overflow', 'auto');
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
        },

        /**
         * 表示領域を設定
         */
        setViewSize: function(){
            let $toolBar = $("#ToolBar");
            let $credit = $("#Credit");
            let $page = $("#Page");
            let $html = $("html");
            $("#Wrap").css('height', '280px');
            $html.height($html.height() - $toolBar.outerHeight() * 1);
            $credit.height($page.height() + $toolBar.outerHeight() * 1);
            $credit.width($page.width());
        },

        /**
         * スクロールバーを非表示に
         */
        resizeScroll: function(){
            let $toolBar = $("#ToolBar");
            let $credit = $("#Credit");
            let $page = $("#Page");
            let $html = $("html");
            let $body = $("body");
            $html.height($html.height() - $toolBar.outerHeight() * 1);
            $body.height($body.height() - $toolBar.outerHeight() * 1);
            $credit.width($page.width());
            $credit.height($page.height() + $toolBar.outerHeight() * 1);
        }
    };
})(jQuery);

