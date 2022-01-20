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
 * baserAjaxBatch 設定
 */
$.extend($.baserAjaxBatch.config, {
    url: '',
    listTable: '#ListTable',
    pageTotalNum: '.page-total-num',
    pageStartNum: '.page-start-num',
    pageEndNum: '.page-end-num',
    dataList: '#DataList',
    methods: {
        /**
         * 削除
         */
        del: {
            confirm: bcI18n.batchListConfirmDeleteMessage,
            result: function () {
                var config = $.baserAjaxBatch.config;
                var colspan = $(config.targetCheckbox + ":checked:first").parent().parent().find('td').length;
                var delNum = $(config.targetCheckbox + ":checked").length;
                $(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) - delNum);
                $(config.pageEndNum).html(Number($(config.pageEndNum).html()) - delNum);
                $(config.targetCheckbox + ":checked").parent().parent().fadeOut(300, function () {
                    $(this).remove();
                    if ($(config.listTable + " tbody td").length) {
                        $.baserAjaxDataList.initList();
                        $(config.listTable + " tbody tr").removeClass('even odd');
                        $.yuga.stripe();
                    } else {
                        $.baserAjaxDataList.load(document.location.href);
                        $(config.listTable + " tbody").append('<td colspan="' + colspan + '"><p class="no-data">データがありません。</p></td>');
                    }
                });
            }
        },
        /**
         * 公開処理
         */
        publish: {
            confirm: bcI18n.batchListConfirmPublishMessage,
            result: function () {
                var config = $.baserAjaxBatch.config;
                var row = $(config.targetCheckbox + ":checked").parent().parent();
                row.removeClass('publish');
                row.removeClass('unpublish');
                row.removeClass('disablerow');
                row.addClass('publish');
                var status = row.find('.status').html().replace(/○|―/gim, '○');
                row.find('.status').html(status);
                $(config.targetCheckbox + ":checked").removeAttr('checked');
                $.baserAjaxDataList.initList();
            }
        },
        /**
         * 非公開処理
         */
        unpublish: {
            confirm: bcI18n.batchListConfirmUnpublishMessage,
            result: function () {
                var config = $.baserAjaxBatch.config;
                var row = $(config.targetCheckbox + ":checked").parent().parent();
                row.removeClass('publish');
                row.removeClass('unpublish');
                row.addClass('disablerow');
                row.addClass('unpublish');
                var status = row.find('.status').html().replace(/○|―/gim, '―');
                row.find('.status').html(status);
                $(config.targetCheckbox + ":checked").removeAttr('checked');
                $.baserAjaxDataList.initList();
            }
        }
    }
});
