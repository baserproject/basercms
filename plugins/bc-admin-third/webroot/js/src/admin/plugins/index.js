$(function () {
    // データリスト設定
    // $.baserAjaxDataList.config.methods.del.confirm = bcI18n.message1;
    // $.baserAjaxDataList.config.methods.del.result = null;
    // $.baserAjaxDataList.config.methods.delfile = {
    //     button: '.btn-delfile',
    //     confirm: bcI18n.message2
    // }
    // 一括処理設定
    // $.baserAjaxBatch.config.methods.del.confirm = bcI18n.message3;
    // $.baserAjaxBatch.config.methods.del.result = null;
    // $.baserAjaxDataList.init();
    // $.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
    // $.baserAjaxSortTable.init({url: $("#AjaxSorttableUrl").html()});

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

});
