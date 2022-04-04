/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツ一覧（テーブル）
 */

$(function () {

    // コピー
    $('.btn-copy').click(function () {
        var url = $(this).attr('href');
        $.ajax({
            url: url,
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
    });

});
