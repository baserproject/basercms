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
    // TODO 実装要
    // $.ajax({
    //     url: $.baseUrl + '/baser/' + $.bcUtil.adminPrefix + '/plugins/ajax_get_market_plugins',
    //     type: "GET",
    //     success: function (result) {
    //         $("#BaserMarket").html(result);
    //     }
    // });

    $("#tabs").tabs();

    $.bcSortable.init({
        updateSortUrl:$("#AdminPluginsIndexScript").attr('data-updateSortUrl')
    });

    $.bcBatch.init({
        batchUrl: $("#AdminPluginsIndexScript").attr('data-batchUrl')
    });

});
