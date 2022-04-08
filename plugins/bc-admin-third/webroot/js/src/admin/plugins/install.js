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
    var resetDbUrl = $("#AdminPluginInstallScript").attr('data-resetDbUrl');
    $("#BtnReset").click(function () {
        if (confirm(bcI18n.message1)) {
            $("#AdminPluginInstallForm").attr('action', resetDbUrl);
            $.bcUtil.showLoader();
        } else {
            return false;
        }
    });
    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });
});

