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
 * baserAjaxBatch プラグイン
 */

(function ($) {
    $.baserAjaxBatch = {
        /**
         * 初期値
         */
        config: {
            url: '',
            executeButton: '#BtnApplyBatch',
            methodSelect: '#ListToolBatch',
            targetCheckbox: '.batch-targets',
            alertBox: '#AlertMessage',
            loader: '#Waiting',
            checkAll: '#ListToolCheckall',
            flashBox: '#flashMessage'
        },
        /**
         * 初期化
         */
        init: function (config) {
            if (config) {
                $.extend($.baserAjaxBatch.config, config);
            }
            return this;
        },
        /**
         * リストの初期化
         */
        initList: function () {

            var config = $.baserAjaxBatch.config;

            // イベント削除
            $($.baserAjaxBatch.config.executeButton).unbind();
            $($.baserAjaxBatch.config.methodSelect).unbind();
            $(config.listTable + " " + config.targetCheckbox).unbind();
            $(config.checkAll).unbind();

            // イベント登録
            $($.baserAjaxBatch.config.executeButton).click(function () {
                if (!$(config.targetCheckbox + ":checked").length) {
                    alert(bcI18n.commonSelectDataFailedMessage);
                    return false;
                }

                var method = $(config.methodSelect).val();
                if (config.methods[method] !== undefined && config.methods[method].confirm && !confirm(config.methods[method].confirm)) {
                    return false;
                }

                var form = $('<form/>').append($(config.targetCheckbox + ":checked").clone());
                form.append($(config.methodSelect).clone().val($(config.methodSelect).val()));

                if (config.methods[method] !== undefined && config.methods[method].beforeSend && !config.methods[method].beforeSend(form)) {
                    return false;
                }

                $.bcToken.check(function () {
                    form.append($('<input name="data[_Token][key]" type="hidden">').val($.bcToken.key));
                    return $.ajax({
                        url: config.url,
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'text',
                        beforeSend: function () {
                            $(config.alertBox).fadeOut(200);
                            $(config.flashBox).parent().fadeOut(200);
                            $(config.loader).show();
                        },
                        success: function (result) {
                            $.bcToken.key = null;
                            $(config.loader).hide();
                            form.remove();
                            if (result) {
                                if (config.methods[result].result !== undefined) {
                                    config.methods[result].result();
                                    $(config.checkAll).prop('checked', false);
                                } else {
                                    $.baserAjaxDataList.load(document.location.href, function () {
                                        $(config.flashBox).html(bcI18n.commonExecCompletedMessage);
                                        $(config.flashBox).parent().fadeIn(500);
                                    });
                                }
                            } else {
                                $(config.alertBox).html(bcI18n.commonBatchExecFailedMessage);
                                $(config.alertBox).fadeIn(500);
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            $.bcToken.key = null;
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
                            $(config.loader).hide();
                            form.remove();
                            $(config.alertBox).html(bcI18n.commonBatchExecFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage);
                            $(config.alertBox).fadeIn(500);
                        }
                    });
                }, {useUpdate: false, hideLoader: false});
                return false;
            });


            $($.baserAjaxBatch.config.methodSelect).change(toolChangeHandler);

            $(config.listTable + " tbody td").click(function () {
                var checkbox = $(this).parent().find(config.targetCheckbox);
                if (!checkbox.prop('checked')) {
                    checkbox.prop('checked', true);
                } else {
                    checkbox.prop('checked', false);
                }
                changeRow(checkbox);
                return false;
            });

            $(config.listTable + " tbody td a").click(function (e) {
                if ($(this).attr('rel') !== 'colorbox') {
                    e.stopPropagation();
                }
            });

            $(config.listTable + " " + config.targetCheckbox).click(function (e) {
                e.stopPropagation();
            });

            $(config.listTable + " " + config.targetCheckbox).change(function () {
                changeRow($(this));
            });

            $(config.checkAll).change(function () {
                if ($(this).prop('checked')) {
                    $(config.listTable + " " + config.targetCheckbox).prop('checked', true);
                } else {
                    $(config.listTable + " " + config.targetCheckbox).prop('checked', false);
                }
                $.baserAjaxBatch.initRowSelected();
            });

            toolChangeHandler();
            $.baserAjaxBatch.initRowSelected();

        },
        /**
         * 行の選択状態を初期化
         */
        initRowSelected: function () {
            var config = $.baserAjaxBatch.config;
            $(config.listTable + " " + config.targetCheckbox).each(function () {
                if ($(this).prop('checked')) {
                    $(this).parent().parent().addClass('selectedrow');
                } else {
                    $(this).parent().parent().removeClass('selectedrow');
                }
            });
        }

    };

    /**
     * バッチ処理ドロップダウン変更時イベント
     */
    function toolChangeHandler() {
        var config = $.baserAjaxBatch.config;
        if ($(config.methodSelect).val()) {
            $(config.executeButton).removeAttr('disabled');
        } else {
            $(config.executeButton).prop('disabled', true);
        }
    }
})(jQuery);

function changeRow(checkbox) {
    if (checkbox.attr('checked') !== undefined) {
        $(checkbox).parent().parent().addClass('selectedrow');
    } else {
        $(checkbox).parent().parent().removeClass('selectedrow');
    }
}
