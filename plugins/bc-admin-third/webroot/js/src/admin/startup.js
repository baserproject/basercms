/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

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
            contentSelector: "$(this).next('.helptext').html()"
        });
    }

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
     * collapse　オプション、詳細設定の折りたたみ開閉
     */
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

    /**
     * Cake\View\Helper\FormHelper
     * @method error()
     * `error`, `errorList` and `errorItem` templatesのclassをjsで変更する
     */
    $(".error-message:has(ul)").removeClass("error-message").addClass("error-wrap");

});

