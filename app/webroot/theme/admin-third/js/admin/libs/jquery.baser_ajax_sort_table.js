/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * baserAjaxSortTable プラグイン
 *
 * 並び替え可能なテーブルを実装する
 *
 * 【必要ライブラリ】
 * jquery / jquery-ui / yuga
 */
(function ($) {

    /* 関数にオプション変数を渡す */
    $.baserAjaxSortTable = {
        config: {
            listTable: ".sort-table",
            handle: ".sort-handle",
            items: "tr.sortable",
            placeHolder: ".placeholder",
            alertBox: "#AlertMessage",
            loader: '#Waiting',
            flashBox: '#flashMessage'
        },
        init: function (config) {
            if (config) {
                $.extend($.baserAjaxSortTable.config, config);
            }
            config = $.baserAjaxSortTable.config;

        },
        initList: function () {

            var config = $.baserAjaxSortTable.config;

            // イベント削除
            $(config.handle).unbind();
            // 初期化されてない場合にメソッドを実行すると処理が止まってしまう為 try を利用
            try {
                $(config.listTable).sortable("destroy");
            } catch (e) {
            }

            // イベント登録
            var sortableOptions = {
                scroll: true,
                items: config.items,
                opacity: 1,
                zIndex: 55,
                containment: 'body',
                tolerance: 'pointer',
                distance: 5,
                cursor: 'move',
                placeholder: 'ui-widget-content placeholder',
                handle: config.handle,
                revert: 100,
                start: sortStartHandler,
                stop: sortStopHander,
                update: sortUpdateHandler
            };
            $(config.handle).css('cursor', 'move');
            $(config.listTable).sortable(sortableOptions);
            $(config.handle).click(function (e) {
                e.stopPropagation();
            });

        }

    };

    /**
     * 並び替え開始時イベント
     */
    function sortStartHandler(event, ui) {

        var config = $.baserAjaxSortTable.config;

        ui.item.css('border', '1px solid #CCC');
        $(config.placeHolder).css('height', ui.item.height());
        for (var i = 0; i < ui.item.find('td').length; i++) {
            $(config.placeHolder).append('<td></td>');
        }
        $(config.placeHolder + " td").css('background-color', '#ffffff');
        $(config.placeHolder + " td").css('border', 'none');

    }

    /**
     * 並び替え終了時イベント
     */
    function sortStopHander(event, ui) {

        ui.item.css('border', 'none');

    }

    /**
     * 並び順を更新時イベント
     */
    function sortUpdateHandler(event, ui) {

        var config = $.baserAjaxSortTable.config;
        var target = ui.item;
        var targetNum = $(config.listTable + " " + config.items).index(target) + 1;
        var sourceNum = target.attr('id').replace('Row', '');
        var offset = targetNum - sourceNum;
        var sortTable = $(config.listTable);

        var form = $('<form/>').hide();
        var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][id]').val(target.find('.id').val());
        var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][offset]').val(offset);
        form.append(sortId).append(sortOffset);

        $.bcToken.check(function () {
            form.append($.bcToken.getHiddenToken());
            var data = form.serialize();
            form.find('input[name="data[_Token][key]"]').remove();
            return $.ajax({
                url: config.url,
                type: 'POST',
                data: data,
                dataType: 'text',
                beforeSend: function () {
                    $(config.alertBox).fadeOut(200);
                    $(config.flashBox).fadeOut(200);
                    $(config.loader).show();
                },
                success: function (result) {
                    if (result == '1') {
                        sortTable.find(config.items).each(function (i, v) {
                            $(this).attr('id', 'Row' + (i + 1));
                        });
                    } else {
                        sortTable.sortable("cancel");
                        $(config.alertBox).html(bcI18n.commonSortSaveFailedMessage);
                        $(config.alertBox).fadeIn(500);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    var errorMessage = '';
                    if (XMLHttpRequest.status == 404) {
                        errorMessage = '<br>' + bcI18n.commonNotFoundProgramMessage;
                    } else {
                        if (XMLHttpRequest.responseText) {
                            errorMessage = '<br>' + XMLHttpRequest.responseText;
                        } else {
                            errorMessage = '<br>' + errorThrown;
                        }
                    }
                    sortTable.sortable("cancel");
                    $(config.alertBox).html(bcI18n.commonSortSaveFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage);
                    $(config.alertBox).fadeIn(500);
                },
                complete: function () {
                    $(config.loader).hide();
                    // $(config.listTable + " " + config.items).removeClass('even odd');
                    // $.yuga.stripe();
                }
            });
        }, {hideLoader: false});

    }

})(jQuery);
