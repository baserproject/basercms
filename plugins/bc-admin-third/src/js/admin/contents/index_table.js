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
 * コンテンツ一覧（テーブル）
 */
const contentsIndexTable = {

    /**
     * mounted
     */
    mounted() {
        this.initView();
        this.registerEvents();
    },

    /**
     * 表示初期化
     */
    initView() {
        // 公開・非公開ボタンの表示設定
        $("#ListTable tbody tr .btn-publish").hide();
        $("#ListTable tbody tr.unpublish .btn-publish").show();
        $("#ListTable tbody tr .btn-unpublish").hide();
        $("#ListTable tbody tr.publish .btn-unpublish").show();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $('.btn-copy, .btn-delete, .btn-publish, .btn-unpublish').click(this.actionClickHandler);
    },

    /**
     * アクションボタンクリック時イベント
     * @returns {boolean}
     */
    actionClickHandler() {
        const $target = $(this);
        if ($target.attr('data-confirm-message') && !confirm($target.attr('data-confirm-message'))) {
            return false;
        }
        let targetUrl = $target.attr('href');
        $.bcToken.check(function () {
            $.ajax({
                url: targetUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $.bcToken.key
                },
                dataType: 'json',
                data: $target.parent().find('form').serialize(),
                beforeSend: function () {
                    $.bcUtil.showLoader();
                }
            }).done(function () {
                $.bcUtil.showNoticeMessage(bcI18n.commonExecCompletedMessage);
                location.reload();
            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                $.bcUtil.showAjaxError(bcI18n.commonExecFailedMessage, XMLHttpRequest, errorThrown);
                $.bcUtil.hideLoader();
                location.href = '#Header';
            });
        });
        return false;
    }

}

contentsIndexTable.mounted();
