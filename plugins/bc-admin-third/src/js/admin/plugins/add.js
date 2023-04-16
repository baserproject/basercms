/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

$(function () {
    $("#BtnSave").click(function () {
        if (confirm(bcI18n.message1)) {
            $.bcUtil.showLoader();
            return true;
        }
        return false;
    });
});
