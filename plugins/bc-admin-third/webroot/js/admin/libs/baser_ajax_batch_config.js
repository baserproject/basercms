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
                $.baserAjaxDataList.load(document.location.href);
            }
        },
        /**
         * 公開処理
         */
        publish: {
            confirm: bcI18n.batchListConfirmPublishMessage,
            result: function () {
                var config = $.baserAjaxBatch.config;
                var row = $(config.targetCheckbox + ":checked").parent().parent().parent();
                row.removeClass('publish');
                row.removeClass('unpublish');
                row.removeClass('disablerow');
                row.addClass('publish');
                row.find('.status').html('○');
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
                var row = $(config.targetCheckbox + ":checked").parent().parent().parent();
                row.removeClass('publish');
                row.removeClass('unpublish');
                row.addClass('disablerow');
                row.addClass('unpublish');
                row.find('.status').html('―');
                $(config.targetCheckbox + ":checked").removeAttr('checked');
                $.baserAjaxDataList.initList();
            }
        }
    }
});
