/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

const updateForm = {

    /**
     * プラグイン名
     */
    plugin: null,

    /**
     * 起動処理
     */
    mounted() {
        this.plugin = $("#AdminPluginsUpdateScript").attr('data-plugin');
        this.registerEvents();
        this.toggleUpdate();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BtnUpdate").on('click', this.update);
        $("#php").on('change', this.toggleUpdate);
    },

    /**
     * アップデート実行
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
        const $phpNotice = $(".php-notice");
        if(updateForm.plugin !== 'BaserCore') return;
        if($("#php").val()) {
            $btnUpdate.removeAttr('disabled');
            $phpNotice.hide();
        } else {
            $btnUpdate.attr('disabled', 'disabled');
            $phpNotice.show();
        }
    }

};

updateForm.mounted();

