/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

$(function () {
    $("#BtnReset").click(function () {
        if (confirm(bcI18n.message1)) {
            $("#PluginAdminInstallForm").attr('action', $("#ResetDbUrl").val());
            $.bcUtil.showLoader();
        } else {
            return false;
        }
    });
    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });
});

