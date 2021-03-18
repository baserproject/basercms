/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader
 * @since           baserCMS v 4.2.0
 * @license         https://basercms.net/license/index.html
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
