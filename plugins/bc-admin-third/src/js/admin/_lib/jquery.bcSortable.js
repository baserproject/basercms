/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

(function ($) {

    $.bcSortable = {
        updateSortUrl : null,
        init: function (config) {
            this.updateSortUrl = config.updateSortUrl
            var sortHandle = $(".sort-handle");
            var sortTable = $(".sort-table");
            // イベント削除
            sortHandle.unbind();
            // 初期化されてない場合にメソッドを実行すると処理が止まってしまう為 try を利用
            try {
                $(sortTable).sortable("destroy");
            } catch (e) {
            }
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
        sortStartHandler: function (event, ui) {
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
            var form = $('<form/>').hide();
            var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'id').val(target.find('.id').val());
            var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'offset').val(offset);
            form.append(sortId).append(sortOffset);

            $.bcToken.check(function () {
                form.append($.bcToken.getHiddenToken());
                var data = form.serialize();
                form.find('input[name="_csrfToken"]').remove();
                return $.ajax({
                    url: $.bcSortable.updateSortUrl,
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    },
                    type: 'POST',
                    data: data,
                    dataType: 'text',
                    beforeSend: function () {
                        $.bcUtil.hideMessage();
                        $.bcUtil.showLoader();
                    },
                    success: function () {
                        sortTable.find("tr.sortable").each(function (i, v) {
                            $(this).attr('id', 'Row' + (i + 1));
                        });
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        var errorMessage = '';
                        if (XMLHttpRequest.status === 404) {
                            errorMessage = '<br>' + bcI18n.commonNotFoundProgramMessage;
                        } else {
                            if (XMLHttpRequest.responseText) {
                                errorMessage = '<br>' + JSON.parse(XMLHttpRequest.responseText).message;
                            } else {
                                errorMessage = '<br>' + errorThrown;
                            }
                        }
                        sortTable.sortable("cancel");
                        $.bcUtil.showAlertMessage(bcI18n.commonBatchExecFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage)
                    },
                    complete: function () {
                        $.bcUtil.hideLoader();
                    }
                });
            }, {hideLoader: false});
        }
    };

})(jQuery);
