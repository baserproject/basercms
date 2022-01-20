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
 * baserAjaxDataList 設定
 */
$.extend($.baserAjaxDataList.config, {
    methods: {
        /**
         * 削除
         */
        del: {
            button: '.btn-delete',
            confirm: bcI18n.commonConfirmHardDeleteMessage,
            result: function (row, result) {
                var config = $.baserAjaxDataList.config;
                if (result) {
                    $(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) - 1);
                    $(config.pageEndNum).html(Number($(config.pageEndNum).html()) - 1);
                    row.fadeOut(300, function () {
                        row.remove();
                        if ($(config.dataList + " tbody td").length) {
                            $.baserAjaxDataList.initList();
                            $(config.dataList + " tbody tr").removeClass('even odd');
                            $.yuga.stripe();
                        } else {
                            $.baserAjaxDataList.load(document.location.href);
                        }
                    });

                } else {
                    $(config.alertBox).html(bcI18n.commonDeleteFailedMessage);
                    $(config.alertBox).fadeIn(500);
                }
            }
        },
        /**
         * コピー
         */
        copy: {
            button: '.btn-copy',
            confirm: '',
            result: function (row, result) {
                var config = $.baserAjaxDataList.config;
                if (result) {

                    $(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) + 1);
                    $(config.pageEndNum).html(Number($(config.pageEndNum).html()) + 1);
                    row.after(result);
                    $.baserAjaxDataList.initList();
                    row.next().hide().fadeIn(300, function () {
                        $(config.dataList + " tbody tr").removeClass('even odd');
                        $.yuga.stripe();
                    });

                } else {
                    $(config.alertBox).html(bcI18n.commonCopyFailedMessage);
                    $(config.alertBox).fadeIn(500);
                }
            }
        },
        /**
         * 公開処理
         */
        publish: {
            button: '.btn-publish',
            confirm: '',
            result: function (row, result) {
                var config = $.baserAjaxDataList.config;
                if (result) {
                    row.removeClass('disablerow');
                    row.removeClass('unpublish');
                    row.addClass('publish');
                    var status = row.find('.status').html().replace(/○|―/gim, '○');
                    row.find('.status').html(status);
                    $.baserAjaxDataList.config.methods.unpublish.initList();
                    $.baserAjaxDataList.config.methods.publish.initList();
                } else {
                    $(config.alertBox).html(bcI18n.commonPublishFailedMessage);
                    $(config.alertBox).fadeIn(500);
                }
            },
            initList: function () {
                var config = $.baserAjaxDataList.config;
                $(config.dataList + " tbody tr .btn-publish").hide();
                $(config.dataList + " tbody tr.unpublish .btn-publish").show();
            }
        },
        /**
         * 非公開処理
         */
        unpublish: {
            button: '.btn-unpublish',
            confirm: '',
            result: function (row, result) {
                var config = $.baserAjaxDataList.config;
                if (result) {
                    row.removeClass('publish');
                    row.addClass('disablerow');
                    row.addClass('unpublish');
                    var status = row.find('.status').html().replace(/○|―/gim, '―');
                    row.find('.status').html(status);
                    $.baserAjaxDataList.config.methods.unpublish.initList();
                    $.baserAjaxDataList.config.methods.publish.initList();
                } else {
                    $(config.alertBox).html(bcI18n.commonUnpublishFailedMessage);
                    $(config.alertBox).fadeIn(500);
                }
            },
            initList: function () {
                var config = $.baserAjaxDataList.config;
                $(config.dataList + " tbody tr .btn-unpublish").hide();
                $(config.dataList + " tbody tr.publish .btn-unpublish").show();
            }
        }
    }
});
