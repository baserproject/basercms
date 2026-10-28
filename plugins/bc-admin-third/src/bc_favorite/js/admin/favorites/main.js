/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

let createApp = require('vue').createApp;
import FavoriteIndex from "./index.vue";

/**
 * よく使う項目の処理を行う
 */

const favoriteList = {

    app: null,

    /**
     * 初期化
     */
    mounted() {
        this.initView();
        this.registerEvents();
    },

    /**
     * 表示初期化
     */
    initView() {
        $("body").append($("#FavoritesMenu"));

        this.app = createApp({
            components: {
                FavoriteIndex
            }
        });

        window.favoriteApp = this.app.mount('#FavoriteMenu');

        $("#BtnFavoriteHelp").bt({
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

        // バリデーション
        $("#FavoriteAjaxForm").validate();

    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnFavoriteAdd").click(function () {
            if (window.favoriteApp && window.favoriteApp.$refs && window.favoriteApp.$refs.favoriteIndex) {
                window.favoriteApp.$refs.favoriteIndex.openModal();
            }
        });
        $("#FavoriteAjaxForm").submit(function () {
            return false
        });
    }
}


favoriteList.mounted();
