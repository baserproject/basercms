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

$(function () {

    initList();

    /**
     * 一覧を初期化
     */
    function initList()
    {
        $('.btn-copy, .btn-delete, .btn-publish, .btn-unpublish').click(actionClickHandler);
        // 公開・非公開ボタンの表示設定
        $("#ListTable tbody tr .btn-publish").hide();
        $("#ListTable tbody tr.unpublish .btn-publish").show();
        $("#ListTable tbody tr .btn-unpublish").hide();
        $("#ListTable tbody tr.publish .btn-unpublish").show();
    }

    /**
     * アクションボタンクリック時イベント
     * @returns {boolean}
     */
    function actionClickHandler()
    {
        if($(this).attr('data-confirm-message') && !confirm($(this).attr('data-confirm-message'))) {
            return false;
        }
        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            headers: {
                'Authorization': $.bcJwt.accessToken
            },
            dataType: 'json',
            data: $(this).parent().find('form').serialize(),
            beforeSend: function () {
                $.bcUtil.showLoader();
            }
        }).done(function () {
            location.reload();
        }).fail(function(XMLHttpRequest, textStatus, errorThrown){
            $.bcUtil.showAjaxError(bcI18n.commonExecFailedMessage, XMLHttpRequest, errorThrown);
            $.bcUtil.hideLoader();
            location.href = '#Header';
        });
        return false;
    }

});
