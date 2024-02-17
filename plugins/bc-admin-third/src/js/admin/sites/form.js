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
 * sites form
 */
const sitesForm = {

    /**
     * 現在のエイリアス
     */
    currentAlias : '',

    /**
     * 初期化
     */
    mounted() {
        this.initView();
        this.alias = $("#alias").val()
    },

    /**
     * 表示初期化
     */
    initView() {
        this.registerEvents();
        this.loadDeviceAndLang();
        this.changeUseDomain();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnSave").click(this.save);
        $("#main-site-id").change(this.loadDeviceAndLang);
        $("#device, #lang").change(this.loadOptions);
        $('input[name="same_main_url"]').click(this.loadOptions);
        $("input[name='use_subdomain']").click(this.changeUseDomain);
    },

    /**
     * 保存
     * @returns {boolean}
     */
    save() {
        if (sitesForm.alias && sitesForm.alias !== $("#alias").val()) {
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
    },

    /**
     * デバイスと言語の表示設定
     */
    loadDeviceAndLang() {
        let currentId = $("#id").val();
        let mainSiteId = $("#main-site-id").val();
        if (mainSiteId === undefined) {
            mainSiteId = 1;
        }
        let url = $.bcUtil.apiAdminBaseUrl + 'baser-core/sites/get_selectable_devices_and_lang/' + mainSiteId;
        if (currentId !== undefined) {
            url += '/' + currentId;
        }
        url += '.json';
        $.bcUtil.ajax(url, function (result) {
            const $selectDevice = $("#device");
            const $selectLang = $("#lang");
            let device = $selectDevice.val();
            let lang = $selectLang.val();
            $selectDevice.find('option').remove();
            $selectLang.find('option').remove();
            result = $.parseJSON(result);
            $.each(result.devices, function (value, name) {
                $selectDevice.append($('<option>').val(value).text(name).prop('selected', (value === device)));
            });
            $.each(result.langs, function (value, name) {
                $selectLang.append($('<option>').val(value).text(name).prop('selected', (value === lang)));
            });
            sitesForm.loadOptions();
        }, {type: 'GET', loaderType: 'after', loaderSelector: '#main-site-id'});
    },

    /**
     * デバイスと言語のオプションの表示設定
     */
    loadOptions() {
        const $autoRedirect = $("#auto-redirect");
        const $sameMainUrl = $("#same-main-url");
        const $autoLink = $("#auto-link");
        const $spanAutoRedirect = $("#SpanSiteAutoRedirect");
        const $spanAutoLink = $("#SpanSiteAutoLink");
        const $device = $("#device");
        if ($device.val() || $("#lang").val()) {
            $("#SectionAccessType").show();
        } else {
            $("#SectionAccessType").hide();
            $autoRedirect.prop('checked', false);
            $sameMainUrl.prop('checked', false);
            $autoLink.prop('checked', false);
        }
        if ($sameMainUrl.prop('checked')) {
            $autoRedirect.prop('checked', false);
            $spanAutoRedirect.hide();
            $autoLink.prop('checked', false);
            $spanAutoLink.hide();
        } else {
            $spanAutoRedirect.show();
            if ($device.val() === 'mobile' || $device.val() === 'smartphone') {
                $spanAutoLink.show();
            } else {
                $spanAutoLink.hide();
            }
        }
    },

    /**
     * サイトで外部ドメイン使用するかどうかの表示切り替え
     */
    changeUseDomain() {
        if ($("input[name='use_subdomain']:checked").val() === '0') {
            //ドメインを利用しない場合は、ドメインタイプは利用しない
            $("#DomainType").hide();
            $("input[name='domain_type']").prop("checked", false);
        } else {
            //ドメインを利用する場合は、ドメインタイプのラジオボタンを表示
            $("#DomainType").show();
            if ($("input[name='domain_type']:checked").val() === undefined) {
                $("#domain-type-1").prop("checked", true);
            }
        }
    }

}

sitesForm.mounted();
