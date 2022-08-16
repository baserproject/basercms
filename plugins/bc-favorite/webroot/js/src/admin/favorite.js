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
 * よく使う項目の処理を行う
 */

$(function () {
    $("#BtnFavoriteAdd").click(function(){
        document.querySelector('#FavoriteListWrap').__vue__.openModal();
    });
    //
    // var currentPageName = $('#FavoriteScript').attr('data-current-page-name');
    // var currentPageUrl = $('#FavoriteScript').attr('data-current-page-url');

    $("body").append($("#FavoritesMenu"));
    //
    // /**
    //  * ダイアログを初期化
    //  */
    // $("#BtnFavoriteAdd").click(function () {
    //     $('#FavoriteDialog').dialog(
    //         {
    //             position: {my: "center", at: "center", of: window},
    //             bgiframe: true,
    //             width: '360px',
    //             modal: true,
    //             open: function (event, ui) {
    //                 if ($(".favorite-menu-list .selected").length == 0) {
    //                     $(this).dialog('option', 'title', bcI18n.favoriteTitle1);
    //                     $("#FavoriteName").val(currentPageName);
    //                     $("#FavoriteUrl").val(currentPageUrl);
    //                 } else {
    //                     $(this).dialog('option', 'title', bcI18n.favoriteTitle2);
    //                     $("#FavoriteId").val($(".favorite-menu-list .selected .favorite-id").val());
    //                     $("#FavoriteName").val($(".favorite-menu-list .selected .favorite-name").val());
    //                     $("#FavoriteUrl").val($(".favorite-menu-list .selected .favorite-url").val());
    //                 }
    //                 // $("#FavoriteAjaxForm").submit();
    //                 $("#FavoriteName").focus();
    //
    //             },
    //             close: function () {
    //                 $("#FavoriteId").val('');
    //                 $("#FavoriteName").val('');
    //                 $("#FavoriteUrl").val('');
    //             },
    //             buttons: [
    //                 {
    //                     text: bcI18n.commonCancel,
    //                     click: function () {
    //                         $(this).dialog('close');
    //                     }
    //                 },
    //                 {
    //                     text: bcI18n.commonSave,
    //                 }
    //             ]
    //         }
    //     );
    //     return false;
    // });
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
    /**
     * お気に入り初期化
     */
    initFavoriteList();
    /**
     * バリデーション
     */
    $("#FavoriteAjaxForm").validate();
    $("#FavoriteAjaxForm").submit(function () {
        return false
    });

    /**
     * 並び替え開始時イベント
     */
    function favoriteSortStartHandler(event, ui) {
        $("ul.favorite-menu-list .placeholder").css('height', ui.item.height());
        ui.item.startIndex = ui.item.index();
    }

    /**
     * 並び順を更新時イベント
     */
    function favoriteSortUpdateHandler(event, ui) {
        var sortTable = $(".favorite-menu-list");
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
                    sortTable.find("li").each(function (index) {
                        $(this).attr('id', 'FavoriteRow' + index);
                    });
                },
                error: function () {
                    sortTable.sortable("cancel");
                    alert(bcI18n.favoriteAlertMessage1);
                },
                complete: function () {
                    $("#Waiting").hide();
                }
            });
        }, {hideLoader: false});
    }

    /**
     * 行を初期化
     */
    function initFavoriteList() {

        // イベント削除
        $(".favorite-menu-list li").unbind();
        //$(".favorite-menu-list li").destroyContextMenu();
        try {
            $(".favorite-menu-list").sortable("destroy");
        } catch (e) {
        }

        // イベント登録
        var favoriteSortableOptions = {
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
            start: favoriteSortStartHandler,
            update: favoriteSortUpdateHandler
        };

        $(".favorite-menu-list").sortable(favoriteSortableOptions);

        var i = 1;
        $(".favorite-menu-list li").each(function () {
            // アクセス制限によってリンクが出力されていない場合はLIごと削除する
            if (!$(this).attr('class').match(/no-data/) && $(this).find('a').html() == null) {
                $(this).remove();
            } else {
                $(this).attr('id', 'FavoriteRow' + (i));
                i++;
            }
        });

    }

});
