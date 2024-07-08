/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {

    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });

    $('input[name="editor"]').click(siteConfigEditorClickHandler);
    siteConfigEditorClickHandler();

    // SMTP送信テスト
    $("#BtnCheckSendmail").click(function () {
        if (!confirm(bcI18n.confirmMessage1)) {
            return false;
        }
        $.bcToken.check(function () {
            return $.ajax({
                type: 'POST',
                url: $.bcUtil.apiAdminBaseUrl + 'baser-core/site_configs/check_sendmail.json',
                data: $("#SiteConfigFormForm").serialize(),
                beforeSend: function () {
                    $("#ResultCheckSendmail").hide();
                    $("#AjaxLoaderCheckSendmail").show();
                },
                success: function (result) {
                    $("#ResultCheckSendmail").html(bcI18n.infoMessage1);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    var errorMessage = '';
                    if (XMLHttpRequest.responseJSON.message) {
                        errorMessage = XMLHttpRequest.responseJSON.message;
                    } else {
                        errorMessage = errorThrown;
                    }
                    $("#ResultCheckSendmail").html(bcI18n.alertMessage1 + errorMessage);
                },
                complete: function () {
                    $("#ResultCheckSendmail").show();
                    $("#AjaxLoaderCheckSendmail").hide();
                }
            });
        }, {loaderType: 'none'});
        return false;
    });

    /**
     * エディタ切替時イベント
     */
    function siteConfigEditorClickHandler() {
        if ($('input[name="editor"]:checked').val() === 'BaserCore.BcCkeditor') {
            $(".ckeditor-option").show();
        } else {
            $(".ckeditor-option").hide();
        }
    }

});
