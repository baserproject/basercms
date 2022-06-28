/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {
    $("#BtnUpdate").click(function () {
        if (confirm(bcI18n.confirmMessage1)) {
            $.bcUtil.showLoader();
            return true;
        }
        return false;
    });
});
