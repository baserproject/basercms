/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サイト編集
 */

$(function () {
    var alias = $("#SiteAlias").val();
    $("#BtnDelete").click(function () {
        if (confirm(bcI18n.confirmMessage1)) {
            var form = $(this).parents('form');
            form.attr('action', $(this).data('action'));
            form.submit();
        }
        return false;
    });
    $("#BtnSave").click(function () {
        if (alias && alias != $("#SiteAlias").val()) {
            $.bcConfirm.show({
                'title': bcI18n.confirmTitle1,
                'message': bcI18n.confirmMessage2,
                'ok': function () {
                    $.bcUtil.showLoader();
                    $("#SiteAdminEditForm").submit();
                }
            });
            return false;
        }
        $.bcUtil.showLoader();
    });

    $("#SiteMainSiteId").change(loadDeviceAndLang);
    $("#SiteDevice, #SiteLang").change(loadOptions);
    $('input[name="data[Site][same_main_url]"]').click(loadOptions);

    loadDeviceAndLang();

    /**
     * デバイスと言語の表示設定
     */
    function loadDeviceAndLang() {
        $.bcUtil.ajax($.baseUrl + '/' + $.bcUtil.adminPrefix + '/sites/ajax_get_selectable_devices_and_lang/' + $("#SiteMainSiteId").val() + '/' + $("#SiteId").val(), function (result) {
            var selectDevice = $("#SiteDevice");
            var selectLang = $("#SiteLang");
            var device = selectDevice.val();
            var lang = selectLang.val();
            selectDevice.find('option').remove();
            selectLang.find('option').remove();
            result = $.parseJSON(result);
            $.each(result.devices, function (value, name) {
                selectDevice.append($('<option>').val(value).text(name).prop('selected', (value === device)));
            });
            $.each(result.langs, function (value, name) {
                selectLang.append($('<option>').val(value).text(name).prop('selected', (value === lang)));
            });
            loadOptions();
        }, {type: 'GET', loaderType: 'after', loaderSelector: '#SiteMainSiteId'});
    }

    /**
     * デバイスと言語のオプションの表示設定
     */
    function loadOptions() {
        var autoRedirect = $("#SiteAutoRedirect");
        var sameMainUrl = $("#SiteSameMainUrl");
        var autoLink = $("#SiteAutoLink");
        var spanAutoRedirect = $("#SpanSiteAutoRedirect");
        var spanAutoLink = $("#SpanSiteAutoLink");
        if ($("#SiteDevice").val() || $("#SiteLang").val()) {
            $("#SectionAccessType").show();
        } else {
            $("#SectionAccessType").hide();
            autoRedirect.prop('checked', false);
            sameMainUrl.prop('checked', false);
            autoLink.prop('checked', false);
        }
        if (sameMainUrl.prop('checked')) {
            autoRedirect.prop('checked', false);
            spanAutoRedirect.hide();
            autoLink.prop('checked', false);
            spanAutoLink.hide();
        } else {
            spanAutoRedirect.show();
            if ($("#SiteDevice").val() == 'mobile' || $("#SiteDevice").val() == 'smartphone') {
                spanAutoLink.show();
            } else {
                spanAutoLink.hide();
            }
        }
    }

});
