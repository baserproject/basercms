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
 * 共通スタートアップ処理
 */

/**
 * ブラウザ判別用
 */
var _ua = (function () {
    return {
        ltIE6: typeof window.addEventListener == "undefined" && typeof document.documentElement.style.maxHeight == "undefined",
        ltIE7: typeof window.addEventListener == "undefined" && typeof document.querySelectorAll == "undefined",
        ltIE8: typeof window.addEventListener == "undefined" && typeof document.getElementsByClassName == "undefined",
        ie: document.uniqueID,
        firefox: window.globalStorage,
        opera: window.opera,
        webkit: !document.uniqueID && !window.opera && !window.globalStorage && window.localStorage,
        mobile: /android|iphone|ipad|ipod/i.test(navigator.userAgent.toLowerCase())
    }
})();

$(function () {
    /**
     * ヘルプ用バルーンチップ設定
     *
     * jQuery / BeautyTips(jQueryプラグイン)が必要
     * ヘルプ対象のタグはクラス名を[help]とし、idは[help+識別子]とする。
     * バルーンチップに表示するテキストのタグは、クラス名をhelptextとし、idを[helptext+識別子]とする。
     */
    if ($('.help').bt) {
        $('.helptext').css('display', 'none');
        $.bt.options.closeWhenOthersOpen = true;
        $('.help').bt({
            trigger: 'click',
            positions: 'top',
            shadow: true,
            shadowOffsetX: 3,
            shadowOffsetY: 3,
            shadowBlur: 8,
            shadowColor: 'rgba(0,0,0,.8)',
            shadowOverlap: false,
            noShadowOpts: {
                strokeStyle: '#999',
                strokeWidth: 3
            },
            width: '360px',
            /*shrinkToFit: true,*/
            spikeLength: 12,
            spikeGirth: 18,
            padding: 15,
            cornerRadius: 0,
            strokeWidth: 6, /*no stroke*/
            strokeStyle: '#690',
            fill: 'rgba(255, 255, 255, 1.00)',
            cssStyles: {
                fontSize: '12px'
            },
            showTip: function (box) {
                $(box).fadeIn(200);
            },
            hideTip: function (box, callback) {
                $(box).animate({
                    opacity: 0
                }, 100, callback);
            },
            contentSelector: "$(this).next('.helptext').html()"
        });
    }
    /**
     * スライド
     * 項目の表示・非表示を切り替える
     */
    $(".slide-trigger").click(function () {
        target = this.id + 'Body';
        if ($("#" + target).css('display') == 'none') {
            $("#" + target).slideDown();
        } else {
            $("#" + target).slideUp();
        }
    });

    $(".btn-slide-form a").click(function () {
        target = this.id + 'Body';
        $(this).parent().fadeOut(300, function () {
            $(this).remove();
            if ($("#" + target).css('display') == 'none') {
                $("#" + target).slideDown();
            } else {
                $("#" + target).slideUp();
            }
        });
    });

    $(".slide-body").hide();

    /**
     * カラーボックス
     */
    if ($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({maxWidth: '60%'});
    /**
     * ポップアップ
     */
    if ($("a[rel='popup']").colorbox) $("a[rel='popup']").colorbox({width: "60%", height: "70%", iframe: true});

    /**
     * ヘルプメニュー
     */
    $('#BtnMenuHelp').click(function () {
        if ($('#Help').css('display') == 'none') {
            $('#Help').fadeIn(300);
        } else {
            $('#Help').fadeOut(300);
        }
    });
    $('#CloseHelp').click(function () {
        $('#Help').fadeOut(300);
    });
    /**
     * 検索ボックス
     */
    $('#BtnMenuSearch').click(function () {
        if ($('#Search').css('display') == 'none') {
            changeSearchBox(true);
            $.ajax({type: "GET", url: $("#SaveSearchBoxUrl").html() + '/1'});
        } else {
            changeSearchBox(false);
            $.ajax({type: "GET", url: $("#SaveSearchBoxUrl").html() + '/'});
        }
    });
    $('#CloseSearch').click(function () {
        $('#Search').fadeOut(300);
        $.ajax({type: "GET", url: $("#SaveSearchBoxUrl").html() + '/'});
    });
    /**
     * 確認リンク
     */
    $(".confirm-link").click(function () {
        if (confirm($(this).attr('confirm'))) {
            alert($(this).attr('link'));
            document.location = $(this).attr('link');
        }
    });
    /**
     * カラーボックス
     */
    if ($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({opacity: 0.8});
    if ($("a[rel='popup']").colorbox) $("a[rel='popup']").colorbox({width: "60%", height: "70%", iframe: true});
    /**
     * 空のサブメニューのLIを削除
     */
    $("#SubMenu li").each(function () {
        if (!$(this).html()) {
            $(this).remove();
        }
    });
    $("input, textarea, select").focus(function () {
        $(this).addClass('active');
    });
    $("input[type=button]").off('focus'); // input type="button"はフォーカス時に activeクラスを追加するイベントを除去
    $("input, textarea, select").focusout(function () {
        $(this).removeClass('active');
    });
    /**
     * よく使う項目
     */
    $('#BtnSideBarOpener').click(btnSideBarOpenerClickHandler);
    /**
     * サブメニュー調整
     * 空の項目を削除する
     */
    $("#SubMenu td ul").each(function () {
        if (!$(this).html().replace(/^\s+|\s+$/g, "")) {
            $(this).parent().parent().remove();
        }
    });
    /**
     * 検索ボックス
     */
    changeSearchBox($("#SearchBoxOpened").html());

    /**
     * トークンの送信が必要なリンクでトークンを送信する
     */
    $.bcToken.replaceLinkToSubmitToken(".submit-token");

});

/**
 * サイドバー開閉ボタンクリック時イベント
 */
function btnSideBarOpenerClickHandler(e) {

    e.stopPropagation();
    if ($('#SideBar').css('position') == 'absolute') {
        changeSidebar(true);
        $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/1'});
    } else {
        changeSidebar(false);
        $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/'});
    }

}

/**
 * サイドバーの開閉切り替え
 */
function changeSidebar(open) {

    if (open) {
        $('#SideBar').show()
            .unbind('click', btnSideBarOpenerClickHandler)
            .css({
                position: 'relative',
                left: '0',
                cursor: 'auto'
            });
        $('#Contents').css('margin-left', '220px');
        $("#BtnSideBarOpener").html('＜');
        $('#FavoriteMenu ul').show();
    } else {
        var height = $('#FavoriteMenu').height();
        $('#SideBar').bind("click", btnSideBarOpenerClickHandler)
            .css({
                cursor: 'pointer',
                position: 'absolute',
                left: '-180px'
            });
        $('#Contents').css('margin-left', '0');
        $("#BtnSideBarOpener").html('＞');
        $('#FavoriteMenu ul').hide();
        $('#FavoriteMenu').height(height);
    }

}

/**
 * 検索ボックスの開閉切り替え
 */
function changeSearchBox(open) {

    if (open) {
        $('#Search').fadeIn(300);
    } else {
        $('#Search').fadeOut(300);
    }

}

/**
 * アラートボックスを表示する
 *
 * 引き数なしの場合は、非表示にする
 */
function alertBox(message) {

    if ($("#AlertMessage").length) {
        if (message) {
            $("#AlertMessage").html(message);
            $("#AlertMessage").fadeIn(500);
        } else {
            $("#AlertMessage").fadeOut(200);
        }
    }

}
