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
 * bcBatch プラグイン
 */

(function ($) {
    $.bcBatch = {
        /**
         * 初期値
         */
        config: {
            batchUrl: '',
            listTable: '#ListTable',
            executeButton: '#BtnApplyBatch',
            methodSelect: '#batch',
            checkAll: '#checkall',
            targetCheckbox: '.batch-targets',
            alertBox: '#AlertMessage',
            loader: '#Waiting',
            flashBox: '#flashMessage'
        },
        /**
         * 初期化
         */
        init: function (config) {
            if (config) {
                $.extend($.bcBatch.config, config);
            }
            this.initList();
            return this;
        },
        /**
         * リストの初期化
         */
        initList: function () {

            var config = $.bcBatch.config;

            // イベント削除
            $($.bcBatch.config.executeButton).unbind();
            $($.bcBatch.config.methodSelect).unbind();
            $(config.listTable + " " + config.targetCheckbox).unbind();
            $(config.checkAll).unbind();

            // イベント登録
            $($.bcBatch.config.executeButton).click(function () {
                if (!$(config.targetCheckbox + ":checked").length) {
                    alert(bcI18n.commonSelectDataFailedMessage);
                    return false;
                }

                if (!confirm(bcI18n.batchConfirmMessage)) {
                    return false;
                }

                var form = $('<form/>').append($(config.methodSelect).clone().val($(config.methodSelect).val()));
                $(config.targetCheckbox + ":checked").each(function(){
                    var value = $(this).attr('value');
                    if(value) {
                        form.append($('<input name="batch_targets[]" type="hidden">').val(value));
                    }
                });

                $.bcToken.check(function () {
                    form.append($('<input name="_csrfToken" type="hidden">').val($.bcToken.key));
                    return $.ajax({
                        url: config.batchUrl,
                        headers: {
                            "Authorization": $.bcJwt.accessToken,
                        },
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'json',
                        beforeSend: function () {
                            $.bcUtil.hideMessage();
                            $.bcUtil.showLoader();
                        },
                        success: function (result) {
                            $.bcUtil.setFlashMessage(result.message);
                            location.reload();
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            $.bcToken.key = null;
                            var errorMessage = '';
                            if (XMLHttpRequest.status === 404) {
                                errorMessage = '<br>' + bcI18n.commonNotFoundProgramMessage;
                            } else {
                                if (XMLHttpRequest.responseText && XMLHttpRequest.responseText !== 'null') {
                                    errorMessage = '<br>' + JSON.parse(XMLHttpRequest.responseText).message;
                                } else {
                                    errorMessage = '<br>' + errorThrown;
                                }
                            }
                            form.remove();
                            $.bcUtil.showAlertMessage(bcI18n.commonBatchExecFailedMessage + '(' + XMLHttpRequest.status + ')' + errorMessage);
                            $.bcUtil.hideLoader();
                        }
                    });
                }, {useUpdate: false, hideLoader: false});
                return false;
            });


            $($.bcBatch.config.methodSelect).change(toolChangeHandler);

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
                $.bcBatch.initRowSelected();
            });

            toolChangeHandler();
            $.bcBatch.initRowSelected();

        },
        /**
         * 行の選択状態を初期化
         */
        initRowSelected: function () {
            var config = $.bcBatch.config;
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
        var config = $.bcBatch.config;
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
