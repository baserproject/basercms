/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
import axios from "axios";

const updateForm = {

    /**
     * プラグイン名
     */
    plugin: null,

    /**
     * vendor / composer に書き込み権限があるか
     */
    isWritablePackage: false,

    /**
     * 起動処理
     */
    mounted() {
        const script = $("#AdminPluginsUpdateScript");
        this.plugin = script.attr('data-plugin');
        this.isWritablePackage = script.attr('data-isWritablePackage');
        this.registerEvents();
        this.toggleUpdate();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnUpdate").on('click', this.update);
        $("#BtnDownload").on('click', $.bcUtil.showLoader);
        $("#php").on('change', this.toggleUpdate);
    },

    /**
     * アップデート実行
     * コアのアップデートの場合、ダウンロードした最新版のファイルを適用してからリクエストを送信する
     * マイグレーションファイルがプログラムに反映されないと実行できないため、別プロセスとして実行する
     * @returns {boolean}
     */
    update() {
        if (confirm(bcI18n.confirmMessage1)) {
            if(updateForm.plugin !== 'BaserCore') {
                $.bcUtil.showLoader();
                return true;
            }
            $.bcToken.check(function() {
                $.bcUtil.showLoader();
                $.bcUtil.hideMessage();
                axios.post($.bcUtil.apiAdminBaseUrl + 'baser-core/plugins/update_core_files.json', {}, {
                    headers: {
                        'X-CSRF-Token': $.bcToken.key
                    }
                })
                .then(response => {
                    let message = response.data.message + bcI18n.updateMessage1;
                    $.bcUtil.showNoticeMessage(message);
                    $(window).scrollTop(0);
                    $.bcUtil.showLoader();
                    // フォーム送信
                    $("#PluginUpdateForm").submit();
                })
                .catch(error => {
                    if (error.response.status === 500) {
                        $.bcUtil.showAlertMessage(error.response.data.message);
                    } else {
                        $.bcUtil.showAlertMessage('予期せぬエラーが発生しました。システム管理者に連絡してください。');
                    }
                    $.bcUtil.hideLoader();
                    $(window).scrollTop(0);
                });
            }, {hideLoader: false});
        }
        return false;
    },

    /**
     * アップデートボタン切り替え
     */
    toggleUpdate() {
        const $btnUpdate = $("#BtnUpdate");
        const $btnDownload = $("#BtnDownload");
        const $phpNotice = $(".php-notice");
        if(updateForm.plugin !== 'BaserCore') return;
        if($("#php").val()) {
            if($btnUpdate.length) $btnUpdate.removeAttr('disabled');
            if($btnDownload.length) $btnDownload.removeAttr('disabled');
            $phpNotice.hide();
        } else {
            if($btnUpdate.length) $btnUpdate.attr('disabled', 'disabled');
            if($btnDownload.length) $btnDownload.attr('disabled', 'disabled');
            $phpNotice.show();
        }
        if(!updateForm.isWritablePackage) {
            if($btnUpdate.length) $btnUpdate.attr('disabled', 'disabled');
            if($btnDownload.length) $btnDownload.attr('disabled', 'disabled');
        }
    }

};

updateForm.mounted();

