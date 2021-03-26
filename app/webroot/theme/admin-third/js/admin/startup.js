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
            shadowOffsetX: 1,
            shadowOffsetY: 1,
            shadowBlur: 8,
            shadowColor: 'rgba(101,101,101,.6)',
            shadowOverlap: false,
            noShadowOpts: {
                strokeStyle: '#999',
                strokeWidth: 1
            },
            width: '600px',
            /*shrinkToFit: true,*/
            spikeLength: 12,
            spikeGirth: 18,
            padding: 20,
            cornerRadius: 0,
            strokeWidth: 1, /*no stroke*/
            strokeStyle: '#656565',
            fill: 'rgba(255, 255, 255, 1.00)',
            cssStyles: {
                fontSize: '14px'
            },
            showTip: function (box) {
                $(box).fadeIn(200);
            },
            hideTip: function (box, callback) {
                $(box).animate({
                    opacity: 0
                }, 100, callback);
            },
            contentSelector: "$(this).nextAll('.helptext').html()"
        });
    }

    /**
     * ツールチップ表示
     * bootstrap4 + jquery
     */
    $('[data-toggle="tooltip"]').tooltip({html: true});


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
     * フォームでエンターキーを押しても送信しないようにする
     */
    $(".bca-form-table input[type=text]").each(function () {
        $(this).keypress(function (e) {
            if (e.which && e.which === 13) {
                return false;
            }
            return true;
        });
    });

    /**
     * 検索ボックス
     */
    changeSearchBox($("#SearchBoxOpened").html());

    /**
     * トークンの送信が必要なリンクでトークンを送信する
     */
    $.bcToken.replaceLinkToSubmitToken(".submit-token");

    /**
     * クリップボードにURLをコピーする
     *
     * @returns false
     */
    var fullUrl = $.bcUtil.frontFullUrl;
    if (!document.queryCommandSupported('copy')) {
        $("#BtnCopyUrl").hide();
    } else if (fullUrl) {
        // URLコピー： クリック後にツールチップの表示内容を切替え
        $("#BtnCopyUrl").on({
            'click': function () {
                var copyArea = $("<textarea style=\" opacity:0; width:1px; height:1px; margin:0; padding:0; border-style: none;\"/>");
                copyArea.text(fullUrl);
                $(this).after(copyArea);
                copyArea.select();
                document.execCommand("copy");
                copyArea.remove();

                // コピー完了のツールチップ表示 bootstrap tooltip
                $("#BtnCopyUrl").tooltip('dispose'); // 一度削除
                $("#BtnCopyUrl").tooltip({title: 'コピーしました'});
                $("#BtnCopyUrl").tooltip('show');
                return false;
            },
            'mouseenter': function () {
                // console.log('マウス ホバー');
                $("#BtnCopyUrl").tooltip('dispose'); // 一度削除
                $("#BtnCopyUrl").tooltip({title: '公開URLをコピー'});
                $("#BtnCopyUrl").tooltip('show');
            },
            'mouseleave': function () {
                // console.log('マウス アウト');
                $("#BtnCopyUrl").tooltip('hide');
            }
        });
    }

    /**
     * collapse　オプション、詳細設定の折りたたみ開閉
     *
     * @returns false
     */
    // URLコピー： クリック後にツールチップの表示内容を切替え
    $("[data-bca-collapse='collapse']").on({
        'click': function () {
            const target = $(this).attr('data-bca-target');
            // data-bca-state属性でtoggle
            if ($(target).attr('data-bca-state') == 'open') {
                // 対象ID要素:非表示
                $(target).attr('data-bca-state', '').slideUp();
                // ボタンの制御
                $(this).attr('data-bca-state', '').attr('aria-expanded', 'true');
            } else {
                // 対象ID要素:表示
                $(target).attr('data-bca-state', 'open').slideDown();
                // ボタンの制御
                $(this).attr('data-bca-state', 'open').attr('aria-expanded', 'false');
            }
            return false;
        }
    });
    $("[data-bca-collapse='favorite-collapse']").on({
        'click': function () {
            const target = $(this).attr('data-bca-target');
            changeOpenFavorite('#btn-favorite-expand', target);
            initFavorite('#btn-favorite-expand', target);
            return false;
        }
    });

    function initFavorite(button, target) {
        if ($(button).attr('data-bca-state') == 'open') {
            $(target).show();
        } else {
            $(target).hide();
        }
    }

    function changeOpenFavorite(button, target) {
        if ($(button).attr('data-bca-state') == 'open') {
            // ボタンの制御
            $(button).attr('data-bca-state', '').attr('aria-expanded', 'true');
            $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/'});
        } else {
            // ボタンの制御
            $(button).attr('data-bca-state', 'open').attr('aria-expanded', 'false');
            $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/1'});
        }
    }

    initFavorite('#btn-favorite-expand', '#favoriteBody');


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
        // $('#Search').fadeIn(300);
        $('#Search').slideDown(300);
    } else {
        // $('#Search').fadeOut(300);
        $('#Search').slideUp(300);
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
