/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */


$(function () {
    var safeModeOn = $("#AdminSiteConfigsFormScript").attr('data-safeModeOn');
    var isAdminSsl = $("#AdminSiteConfigsFormScript").attr('data-isAdminSsl');
    /**
     * 「保存」ボタンを押下した際の動作
     */
    $("#BtnSave").click(function () {
        if (!isSafeModeCheck()) {
            return false;
        }
        if (!isAdminSslCheck()) {
            return false;
        }
        $.bcUtil.showLoader();
    });

    function isAdminSslCheck() {
        if (isAdminSsl == "0" && $("input[name='data[SiteConfig][admin_ssl]']:checked").val() == "1") {
            if (!$("#SiteConfigSslUrl").val()) {
                alert(bcI18n.alertMessage1);
                window.location.hash = 'SiteConfigSslUrl';
                return false;
            }
            var adminSslAlert = bcI18n.confirmMessage1;
            $.bcConfirm.show({
                title: bcI18n.confirmTitle1,
                message: adminSslAlert,
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

    function isSafeModeCheck() {
        var theme = $("#SiteConfigTheme").val();
        var safemodeAlert = bcI18n.alertMessage2;

        if (safeModeOn && (theme != $("#SiteConfigTheme").val())) {
            if (!confirm(safemodeAlert)) {
                return false;
            }
        }
        return true;
    }

    // SMTP送信テスト
    $("#BtnCheckSendmail").click(function () {
        if (!confirm(bcI18n.confirmMessage2)) {
            return false;
        }
        $.bcToken.check(function () {
            return $.ajax({
                type: 'POST',
                url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/site_configs/check_sendmail',
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
                    $("#ResultCheckSendmail").html(bcI18n.alertMessage3 + errorMessage);
                },
                complete: function () {
                    $("#ResultCheckSendmail").show();
                    $("#AjaxLoaderCheckSendmail").hide();
                }
            });
        }, {loaderType: 'none'});
        return false;
    });

    $("#SiteConfigMobile").click(function () {
        if ($("#SiteConfigMobile").prop('checked')) {
            $("#SpanLinkedPagesMobile").show();
            $("#SpanRootLayoutTemplateMobile").show();
            $("#SpanRootContentTemplateMobile").show();
        } else {
            $("#SpanLinkedPagesMobile").hide();
            $("#SpanRootLayoutTemplateMobile").hide();
            $("#SpanRootContentTemplateMobile").hide();
            $('#SiteConfigLinkedPagesMobile0').prop('checked', 'checked');
        }
    });
    $("#SiteConfigSmartphone").click(function () {
        if ($("#SiteConfigSmartphone").prop('checked')) {
            $("#SpanLinkedPagesSmartphone").show();
            $("#SpanRootLayoutTemplateSmartphone").show();
            $("#SpanRootContentTemplateSmartphone").show();
        } else {
            $("#SpanLinkedPagesSmartphone").hide();
            $("#SpanRootLayoutTemplateSmartphone").hide();
            $("#SpanRootContentTemplateSmartphone").hide();
            $('#SiteConfigLinkedPagesSmartphone0').prop('checked', 'checked');
        }
    });

    $('input[name="data[SiteConfig][editor]"]').click(siteConfigEditorClickHandler);

    if (!$("#SiteConfigMobile").prop('checked')) {
        $("#SpanLinkedPagesMobile").hide();
        $("#SpanRootLayoutTemplateMobile").hide();
        $("#SpanRootContentTemplateMobile").hide();
    }
    if (!$("#SiteConfigSmartphone").prop('checked')) {
        $("#SpanLinkedPagesSmartphone").hide();
        $("#SpanRootLayoutTemplateSmartphone").hide();
        $("#SpanRootContentTemplateSmartphone").hide();
    }

    siteConfigEditorClickHandler();

    function siteConfigEditorClickHandler() {
        if ($('input[name="data[SiteConfig][editor]"]:checked').val() === 'BcCkeditor') {
            $(".ckeditor-option").show();
        } else {
            $(".ckeditor-option").hide();
        }
    }

});
