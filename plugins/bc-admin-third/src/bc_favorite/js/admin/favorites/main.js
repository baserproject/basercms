/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

import Vue from 'vue/dist/vue.js'
import FavoriteIndex from "./index.vue";
import Vuelidate from 'vuelidate'


/**
 * よく使う項目の処理を行う
 */

const favoriteList = {

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

        Vue.use(Vuelidate)
        new Vue({
            el: '#FavoriteMenu',
            components: {
                FavoriteIndex
            },
        });

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

        // お気に入り初期化
        this.initFavoriteList();

        // バリデーション
        $("#FavoriteAjaxForm").validate();

    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnFavoriteAdd").click(function () {
            document.querySelector('#FavoriteListWrap').__vue__.openModal();
        });
        $("#FavoriteAjaxForm").submit(function () {
            return false
        });
    },

    /**
     * 並び替え開始時イベント
     */
    favoriteSortStartHandler(event, ui) {
        $("ul.favorite-menu-list .placeholder").css('height', ui.item.height());
        ui.item.startIndex = ui.item.index();
    },

    /**
     * 並び順を更新時イベント
     */
    favoriteSortUpdateHandler(event, ui) {
        var $sortTable = $(".favorite-menu-list");
        var offset = ui.item.index() - ui.item.startIndex;
        var id = ui.item.attr('data-id');

        $.bcToken.check(function () {
            var data = {
                'id': id,
                'offset': offset,
                '_csrfToken': $.bcToken.key
            };
            return $.ajax({
                url: $.bcUtil.apiBaseUrl + 'bc-favorite/favorites/change_sort.json',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
                type: 'POST',
                data: data,
                dataType: 'text',
                beforeSend: function () {
                    $("#Waiting").show();
                },
                success: function (result) {
                    $sortTable.find("li").each(function (index) {
                        $(this).attr('id', 'FavoriteRow' + index);
                    });
                },
                error: function () {
                    $sortTable.sortable("cancel");
                    alert(bcI18n.favoriteAlertMessage1);
                },
                complete: function () {
                    $("#Waiting").hide();
                }
            });
        }, {hideLoader: false});
    },

    /**
     * 行を初期化
     */
    initFavoriteList() {
        const $favoriteMenuList = $(".favorite-menu-list");
        const $favoriteMenuListLi = $(".favorite-menu-list li");

        // イベント削除
        $favoriteMenuListLi.unbind();
        try {
            $favoriteMenuList.sortable("destroy");
        } catch (e) {
        }

        $favoriteMenuList.sortable({
            scroll: true,
            opacity: 0.80,
            zIndex: 55,
            containment: 'body',
            tolerance: 'pointer',
            distance: 5,
            cursor: 'pointer',
            placeholder: 'ui-widget-content placeholder',
            /*handle: ".favorite-menu-list li a",*/
            revert: 100,
            start: this.favoriteSortStartHandler,
            update: this.favoriteSortUpdateHandler
        });

        var i = 1;
        $favoriteMenuListLi.each(function () {
            // アクセス制限によってリンクが出力されていない場合はLIごと削除する
            if (!$(this).attr('class').match(/no-data/) && $(this).find('a').html() == null) {
                $(this).remove();
            } else {
                $(this).attr('id', 'FavoriteRow' + (i));
                i++;
            }
        });
    }
}


favoriteList.mounted();
