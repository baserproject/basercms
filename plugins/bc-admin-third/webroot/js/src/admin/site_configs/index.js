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

    var script = $("#AdminSiteConfigsFormScript");
    var isAdminSsl = script.attr('data-isAdminSsl');

    $("#BtnSave").click(function () {
        if (!isAdminSslCheck(isAdminSsl)) {
            return false;
        }
        $.bcUtil.showLoader();
    });

    $('input[name="editor"]').click(siteConfigEditorClickHandler);
    siteConfigEditorClickHandler();

    // SMTP送信テスト
    $("#BtnCheckSendmail").click(function () {
        if (!confirm(bcI18n.confirmMessage2)) {
            return false;
        }
        $.bcToken.check(function () {
            return $.ajax({
                type: 'POST',
                url: $.bcUtil.apiBaseUrl + 'baser-core/site_configs/check_sendmail.json',
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                },
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
                    if (XMLHttpRequest.responseText) {
                        errorMessage = XMLHttpRequest.responseText;
                    } else {
                        errorMessage = errorThrown;
                    }
                    $("#ResultCheckSendmail").html(bcI18n.alertMessage2 + errorMessage);
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
     * 管理画面SSLチェック
     * @param isAdminSsl
     * @returns {boolean}
     */
    function isAdminSslCheck(isAdminSsl) {
        if (isAdminSsl === "0" && $("input[name='admin_ssl']:checked").val() === "1") {
            if (!$("#SiteConfigSslUrl").val()) {
                alert(bcI18n.alertMessage1);
                window.location.hash = 'ssl-url';
                return false;
            }
            $.bcConfirm.show({
                title: bcI18n.confirmTitle1,
                message: bcI18n.confirmMessage1,
                defaultCancel: true,
                ok: function () {
                    $.bcUtil.showLoader();
                    $("#SiteConfigFormForm").submit();
                }
            });
            return false;
        }
        return true;
    }

    /**
     * エディタ切替時イベント
     */
    function siteConfigEditorClickHandler() {
        if ($('input[name="editor"]:checked').val() === 'BcCkeditor') {
            $(".ckeditor-option").show();
        } else {
            $(".ckeditor-option").hide();
        }
    }

});
