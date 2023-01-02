/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var i=$("#AdminPermissionGroupsIndexScript").attr("data-userGroupId");$('input[name="list_type"]').click((function(){$.bcUtil.showLoader(),location.href="".concat($.bcUtil.adminBaseUrl,"baser-core/permission_groups/index/").concat(i,"?list_type=").concat($(this).val())}))}));
//# sourceMappingURL=index.bundle.js.map