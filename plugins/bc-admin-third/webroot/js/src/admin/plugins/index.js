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
     * マーケットのデータを取得
     */
    $.ajax({
        url: $.baseUrl() + '/baser' + $.bcUtil.adminPrefix + '/baser-core/plugins/get_market_plugins',
        type: "GET",
        success: function (result) {
            $("#BaserMarket").html(result);
        }
    });

    /**
     * タブ設定
     */
    $("#tabs").tabs();

    /**
     * 並び替え機能実装
     */
    $.bcSortable.init({
        updateSortUrl:$("#AdminPluginsIndexScript").attr('data-updateSortUrl')
    });

    /**
     * 一括処理実装
     */
    $.bcBatch.init({
        batchUrl: $("#AdminPluginsIndexScript").attr('data-batchUrl')
    });

});
