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
 * テーブル表示モード時のアップロードファイルの削除処理
 */
$(function () {
    $("#FileList").bind('loadTableComplete', function () {
        $(".btn-delete").click(function () {
            if (confirm(bcI18n.uploaderConfirmMessage1)) {
                $.bcToken.submitToken($(this).attr('href'));
            }
            return false;
        });
    });
});
