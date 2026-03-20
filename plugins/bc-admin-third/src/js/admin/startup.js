/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

import { Tooltip } from "bootstrap";
import "jquery-colorbox";

/**
 * ブラウザ判別用
 * TOOD ucmitz 未検証
 */
// var _ua = (function () {
//     return {
//         ltIE6: typeof window.addEventListener == "undefined" && typeof document.documentElement.style.maxHeight == "undefined",
//         ltIE7: typeof window.addEventListener == "undefined" && typeof document.querySelectorAll == "undefined",
//         ltIE8: typeof window.addEventListener == "undefined" && typeof document.getElementsByClassName == "undefined",
//         ie: document.uniqueID,
//         firefox: window.globalStorage,
//         opera: window.opera,
//         webkit: !document.uniqueID && !window.opera && !window.globalStorage && window.localStorage,
//         mobile: /android|iphone|ipad|ipod/i.test(navigator.userAgent.toLowerCase())
//     }
// })();

/**
 * String 拡張
 * sprintf の文字列置き換えのみ対応
 *
 * @returns {string}
 */
String.prototype.sprintf = function () {
    var str = this + '';
    var args = Array.prototype.slice.call(arguments);

    var ph = true;
    if (str.indexOf('%s', 0) !== -1) {
        ph = false;
    }

    if (args.length === 1) {
        if (ph) {
            return str.replace(/%1$s/g, args[0]);
        } else {
            return str.replace(/%s/g, args[0]);
        }
    } else {
        for (var i = 0; i < args.length; i++) {
            var n = i + 1;
            if (ph) {
                str = str.replace('%' + n + '$s', args[i]);
            } else {
                str = str.replace('%s', args[i]);
            }
        }
    }
    return str;
};


/**
 * ヘルプ用バルーンチップ設定
 *
 * jQuery / BeautyTips(jQueryプラグイン)が必要
 * ヘルプ対象のタグはクラス名を[help]とし、idは[help+識別子]とする。
 * バルーンチップに表示するテキストのタグは、クラス名をhelptextとし、idを[helptext+識別子]とする。
 */
$.bcUtil.initTooltip();

/**
 * ツールチップ表示
 * bootstrap4 + jquery
 * TODO umictz 未検証
 */
// $('[data-toggle="tooltip"]').tooltip({html: true});

/**
 * ポップアップ
 * TODO umictz 未検証
 */
// if ($("a[rel='popup']").colorbox) $("a[rel='popup']").colorbox({width: "60%", height: "70%", iframe: true});

/**
 * スライド
 * 項目の表示・非表示を切り替える
 * TODO umictz 未検証
 */
// $(".slide-trigger").click(function () {
//     target = this.id + 'Body';
//     if ($("#" + target).css('display') == 'none') {
//         $("#" + target).slideDown();
//     } else {
//         $("#" + target).slideUp();
//     }
// });
//
// $(".btn-slide-form a").click(function () {
//     target = this.id + 'Body';
//     $(this).parent().fadeOut(300, function () {
//         $(this).remove();
//         if ($("#" + target).css('display') == 'none') {
//             $("#" + target).slideDown();
//         } else {
//             $("#" + target).slideUp();
//         }
//     });
// });
//
// $(".slide-body").hide();

/**
 * 確認リンク
 * TODO umictz 未検証
 */
// $(".confirm-link").click(function () {
//     if (confirm($(this).attr('confirm'))) {
//         alert($(this).attr('link'));
//         document.location = $(this).attr('link');
//     }
// });

/**
 * 空のサブメニューのLIを削除
 * TODO umictz 未検証
 */
// $("#SubMenu li").each(function () {
//     if (!$(this).html()) {
//         $(this).remove();
//     }
// });
// $("input, textarea, select").focus(function () {
//     $(this).addClass('active');
// });
// $("input[type=button]").off('focus'); // input type="button"はフォーカス時に activeクラスを追加するイベントを除去
// $("input, textarea, select").focusout(function () {
//     $(this).removeClass('active');
// });

/**
 * サブメニュー調整
 * 空の項目を削除する
 * TODO umictz 未検証
 */
// $("#SubMenu td ul").each(function () {
//     if (!$(this).html().replace(/^\s+|\s+$/g, "")) {
//         $(this).parent().parent().remove();
//     }
// });

/**
 * フォームでエンターキーを押しても送信しないようにする
 * TODO umictz 未検証
 */
// $(".bca-form-table input[type=text]").each(function () {
//     $(this).keypress(function (e) {
//         if (e.which && e.which === 13) {
//             return false;
//         }
//         return true;
//     });
// });

/**
 * トークンの送信が必要なリンクでトークンを送信する
 */
$.bcToken.replaceLinkToSubmitToken(".bca-submit-token");

/**
 * カラーボックス
 */
if ($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({maxWidth: '60%'});

/**
 * ヘルプ
 */
$('#BtnMenuHelp').click(function () {
    if ($('#Help').css('display') === 'none') {
        $('#Help').fadeIn(300);
    } else {
        $('#Help').fadeOut(300);
    }
});
$('#CloseHelp').click(function () {
    $('#Help').fadeOut(300);
});

/**
 * bcUtil 初期化
 */
$.bcUtil.init({});

/**
 * bcToken 初期化
 */
$.bcToken.init();

/**
 * bcJwt 初期化
 */
$.bcJwt.init();

/**
 * collapse　オプション、詳細設定の折りたたみ開閉
 */

// アコーディオン開閉状態の保存・復元（プラグイン＋コントローラー単位で保存）
function getAccordionStorageKey() {
    const path = location.pathname;
    // パスを分割して最初の4セグメント（/baser/admin/plugin/controller）まで取得
    const segments = path.split('/').filter(s => s); // 空文字を除外
    const keyPath = segments.slice(0, 4).join('/');
    return 'bc-admin-third-ac_state-/' + keyPath;
}

function loadAccordionState() {
    try {
        return JSON.parse(localStorage.getItem(getAccordionStorageKey()) || '{}');
    } catch (e) {
        return {};
    }
}

function saveAccordionState(targetSel, isOpen) {
    try {
        const state = loadAccordionState();
        state[targetSel] = isOpen;
        localStorage.setItem(getAccordionStorageKey(), JSON.stringify(state));
    } catch (e) {
        // localStorage が使えない環境では何もしない
    }
}

// 初期状態の復元
const state = loadAccordionState();
$("[data-bca-collapse='collapse']").each(function() {
    const btn = $(this);
    const targetSel = btn.attr('data-bca-target');
    if (!targetSel) return;

    const target = $(targetSel);
    // localStorageに保存された状態があればそれを適用
    if (state.hasOwnProperty(targetSel)) {
        const isOpen = state[targetSel];
        if (isOpen) {
            target.attr('data-bca-state', 'open').show();
            btn.attr('data-bca-state', 'open').attr('aria-expanded', 'false');
        } else {
            target.attr('data-bca-state', '').hide();
            btn.attr('data-bca-state', '').attr('aria-expanded', 'true');
        }
    }
});

// 開閉イベント
$("[data-bca-collapse='collapse']").on({
    'click': function () {
        const targetSel = $(this).attr('data-bca-target');
        if (!targetSel) return false;

        const target = $(targetSel);
        // data-bca-state属性でtoggle
        if (target.attr('data-bca-state') == 'open') {
            // 対象ID要素:非表示
            target.attr('data-bca-state', '').slideUp();
            // ボタンの制御
            $(this).attr('data-bca-state', '').attr('aria-expanded', 'true');
            // localStorage に保存
            saveAccordionState(targetSel, false);
        } else {
            // 対象ID要素:表示
            target.attr('data-bca-state', 'open').slideDown();
            // ボタンの制御
            $(this).attr('data-bca-state', 'open').attr('aria-expanded', 'false');
            // localStorage に保存
            saveAccordionState(targetSel, true);
        }
        return false;
    }
});

/**
 * Cake\View\Helper\FormHelper
 * @method error()
 * `error`, `errorList` and `errorItem` templatesのclassをjsで変更する
 */
$(".error-message:has(ul)").removeClass("error-message").addClass("error-wrap");

/**
 * クリップボードにURLをコピーする
 *
 * @returns false
 */
let fullUrl = $.bcUtil.frontFullUrl;
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
            Tooltip.getInstance(this)?.dispose();
            new Tooltip(this, {title: 'コピーしました', trigger: 'manual'}).show();
            return false;
        },
        'mouseenter': function () {
            new Tooltip(this, {title: '公開URLをコピー', trigger: 'hover'}).show();
        },
        'mouseleave': function () {
            Tooltip.getInstance(this)?.hide();
        }
    });
}

/**
 * よく使う項目
 * TODO ucmitz 未検証
 */
// $('#BtnSideBarOpener').click(btnSideBarOpenerClickHandler);

/**
 * サイドバー開閉ボタンクリック時イベント
 * TODO ucmitz 未検証
 */
// function btnSideBarOpenerClickHandler(e) {
//     e.stopPropagation();
//     if ($('#SideBar').css('position') == 'absolute') {
//         changeSidebar(true);
//         $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/1'});
//     } else {
//         changeSidebar(false);
//         $.ajax({type: "GET", url: $("#SaveFavoriteBoxUrl").html() + '/'});
//     }
// }

/**
 * サイドバーの開閉切り替え
 * TODO ucmitz 未検証
 */
// function changeSidebar(open) {
//     if (open) {
//         $('#SideBar').show()
//             .unbind('click', btnSideBarOpenerClickHandler)
//             .css({
//                 position: 'relative',
//                 left: '0',
//                 cursor: 'auto'
//             });
//         $('#Contents').css('margin-left', '220px');
//         $("#BtnSideBarOpener").html('＜');
//         $('#FavoriteMenu ul').show();
//     } else {
//         var height = $('#FavoriteMenu').height();
//         $('#SideBar').bind("click", btnSideBarOpenerClickHandler)
//             .css({
//                 cursor: 'pointer',
//                 position: 'absolute',
//                 left: '-180px'
//             });
//         $('#Contents').css('margin-left', '0');
//         $("#BtnSideBarOpener").html('＞');
//         $('#FavoriteMenu ul').hide();
//         $('#FavoriteMenu').height(height);
//     }
// }

// クリック時にローディング表示
$('.bca-loading').click(function () {
    $.bcUtil.showLoader();
});

$.bcUtil.showFlashMessage();



