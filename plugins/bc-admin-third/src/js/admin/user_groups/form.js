/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


if ($("#AdminUserGroupsFormScript").attr('data-isAdmin')) {
    $("#auth-prefix-admin").prop('disabled', true);
}

$("#UserGroupAdminEditForm").submit(function () {
    $("#auth-prefix-admin").removeAttr('disabled');
});

$("#BtnSave").click(function () {
    $.bcUtil.showLoader();
});
