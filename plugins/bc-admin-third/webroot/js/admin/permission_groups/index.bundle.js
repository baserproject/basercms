(()=>{
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
var i={initialUserGroupId:0,mounted:function(){this.initialUserGroupId=$("#filter-user-group-id").val(),this.registerEvents()},registerEvents:function(){$("#filter-user-group-id").change(this.changeList),$('input[name="list_type"]').click(this.changeList)},changeList:function(){$.bcUtil.showLoader();var e=$("#filter-user-group-id").val(),t=$('input[name="list_type"]:checked').val();i.initialUserGroupId===e?location.href="".concat($.bcUtil.adminBaseUrl,"baser-core/permission_groups/index/").concat(e,"?list_type=").concat(t):location.href="".concat($.bcUtil.adminBaseUrl,"baser-core/permission_groups/index/").concat(e)}};i.mounted()})();
//# sourceMappingURL=index.bundle.js.map