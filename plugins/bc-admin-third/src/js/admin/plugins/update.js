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
     * アップデートできるかどうか
     */
    isUpdatable: false,

    /**
     * 起動処理
     */
    mounted() {
        const script = $("#AdminPluginsUpdateScript");
        this.plugin = script.attr('data-plugin');
        this.isUpdatable = script.attr('data-isUpdatable');
        if(this.isUpdatable === undefined) this.isUpdatable = false;
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
            $.bcUtil.showLoader();
            return true;
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
        const $inputPhp = $("#php");

        if (updateForm.plugin === 'BaserCore') {
            if ($inputPhp.val() !== ''){
                if(updateForm.isUpdatable) {
                    $btnUpdate.removeAttr('disabled');
                    $btnDownload.removeAttr('disabled');
                } else {
                    $btnUpdate.attr('disabled', 'disabled');
                    $btnDownload.attr('disabled', 'disabled');
                }
            } else {
                $btnUpdate.attr('disabled', 'disabled');
                $btnDownload.attr('disabled', 'disabled');
            }
            if ($inputPhp.val()) {
                $phpNotice.hide();
            } else {
                $phpNotice.show();
            }
        } else {
            if (updateForm.isUpdatable) {
                $btnUpdate.removeAttr('disabled');
                $btnDownload.removeAttr('disabled');
            } else {
                $btnUpdate.attr('disabled', 'disabled');
                $btnDownload.attr('disabled', 'disabled');
            }
        }
    }

};

updateForm.mounted();

