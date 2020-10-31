$(function() {
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
});

