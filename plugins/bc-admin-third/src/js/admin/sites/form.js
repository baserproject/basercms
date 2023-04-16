/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * サイト編集
 */

$(function () {
    var alias = $("#alias").val();
    $("#BtnSave").click(function () {
        if (alias && alias != $("#alias").val()) {
            $.bcConfirm.show({
                'title': bcI18n.confirmTitle1,
                'message': bcI18n.confirmMessage2,
                'ok': function () {
                    $.bcUtil.showLoader();
                    $("#BtnSave").parents("form").submit();
                }
            });
            return false;
        }
        $.bcUtil.showLoader();
    });

    $("#main-site-id").change(loadDeviceAndLang);
    $("#device, #lang").change(loadOptions);
    $('input[name="same_main_url"]').click(loadOptions);

    loadDeviceAndLang();

    /**
     * デバイスと言語の表示設定
     */
    function loadDeviceAndLang() {
        var currentId = $("#id").val();
        var mainSiteId = $("#main-site-id").val();
        if(mainSiteId === undefined) {
            mainSiteId = 1;
        }
        var url = $.bcUtil.apiAdminBaseUrl + 'baser-core/sites/get_selectable_devices_and_lang/' + mainSiteId;
        if(currentId !== undefined) {
            url += '/' + currentId;
        }
        url += '.json';
        $.bcUtil.ajax(url, function (result) {
            var selectDevice = $("#device");
            var selectLang = $("#lang");
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
        }, {type: 'GET', loaderType: 'after', loaderSelector: '#main-site-id'});
    }

    /**
     * デバイスと言語のオプションの表示設定
     */
    function loadOptions() {
        var autoRedirect = $("#auto-redirect");
        var sameMainUrl = $("#same-main-url");
        var autoLink = $("#auto-link");
        var spanAutoRedirect = $("#SpanSiteAutoRedirect");
        var spanAutoLink = $("#SpanSiteAutoLink");
        if ($("#device").val() || $("#lang").val()) {
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
            if ($("#device").val() == 'mobile' || $("#device").val() == 'smartphone') {
                spanAutoLink.show();
            } else {
                spanAutoLink.hide();
            }
        }
    }

});
