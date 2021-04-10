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

    var updateSortUrl = $("#AdminPluginsIndexScript").attr('data-updateSortUrl');
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

    /* 関数にオプション変数を渡す */
    var sort = {
        initList: function () {
            var sortHandle = $(".sort-handle");
            var sortTable = $(".sort-table");
            // イベント削除
            sortHandle.unbind();
            // 初期化されてない場合にメソッドを実行すると処理が止まってしまう為 try を利用
            try {
                $(sortTable).sortable("destroy");
            } catch (e) {}
            // イベント登録
            var sortableOptions = {
                scroll: true,
                items: "tr.sortable",
                opacity: 1,
                zIndex: 55,
                containment: 'body',
                tolerance: 'pointer',
                distance: 5,
                cursor: 'move',
                handle: ".sort-handle",
                placeholder: 'ui-sortable-placeholder',
                revert: 100,
                start: this.sortStartHandler,
                update: this.sortUpdateHandler
            };
            sortHandle.css('cursor', 'move');
            sortTable.sortable(sortableOptions);
            sortHandle.click(function (e) {
                e.stopPropagation();
            });
        },

        /**
         * 並び替え開始時イベント
         */
        sortStartHandler : function (event, ui) {
            var placeholder = $(".ui-sortable-placeholder");
            placeholder.css('height', ui.item.height());
        },

        /**
         * 並び順を更新時イベント
         */
        sortUpdateHandler: function (event, ui) {
            var target = ui.item;
            var targetNum = $(".sort-table" + " " + "tr.sortable").index(target) + 1;
            var sourceNum = target.attr('id').replace('Row', '');
            var offset = targetNum - sourceNum;
            var sortTable = $(".sort-table");
            var alertMessage = $("#AlertMessage");
            var form = $('<form/>').hide();
            var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'Sort[id]').val(target.find('.id').val());
            var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'Sort[offset]').val(offset);
            form.append(sortId).append(sortOffset);

            $.bcToken.check(function () {
                form.append($.bcToken.getHiddenToken());
                var data = form.serialize();
                form.find('input[name="_Token[key]"]').remove();
                return $.ajax({
                    url: updateSortUrl,
                    type: 'POST',
                    data: data,
                    dataType: 'text',
                    beforeSend: function () {
                        alertMessage.fadeOut(200);
                        $('#flashMessage').fadeOut(200);
                        $.bcUtil.showLoader();
                    },
                    success: function (result) {
                        if (result === '1') {
                            sortTable.find("tr.sortable").each(function (i, v) {
                                $(this).attr('id', 'Row' + (i + 1));
                            });
                        } else {
                            sortTable.sortable("cancel");
                            alertMessage.html(bcI18n.commonSortSaveFailedMessage);
                            alertMessage.fadeIn(500);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        var errorMessage = '';
                        if (XMLHttpRequest.status === 404) {
                            errorMessage = '<br>' + bcI18n.commonNotFoundProgramMessage;
                        } else {
                            if (XMLHttpRequest.responseText) {
                                errorMessage = '<br>' + XMLHttpRequest.responseText;
                            } else {
                                errorMessage = '<br>' + errorThrown;
                            }
                        }
                        sortTable.sortable("cancel");
                        alertMessage.html(bcI18n.commonSortSaveFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage);
                        alertMessage.fadeIn(500);
                    },
                    complete: function () {
                        $.bcUtil.hideLoader();
                    }
                });
            }, {hideLoader: false});
        }
    };

    sort.initList();

});
