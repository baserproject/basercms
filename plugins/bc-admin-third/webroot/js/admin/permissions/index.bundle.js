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
var i={permissionGroups:[],userGroupId:null,mounted:function(){var i=$("#AdminPermissionsIndexScript");this.userGroupId=i.attr("data-userGroupId"),this.permissionGroups=JSON.parse(i.attr("data-permissionGroups")),this.initView()},initView:function(){$.bcSortable.init({updateSortUrl:$.bcUtil.apiAdminBaseUrl+"baser-core/permissions/update_sort/"+this.userGroupId+".json"}),$.bcBatch.init({batchUrl:$.bcUtil.apiAdminBaseUrl+"baser-core/permissions/batch.json"}),this.initPermissionGroups(),this.registerEvents()},registerEvents:function(){console.log("event"),$("input[name='permission_group_type']").click(this.initPermissionGroups)},initPermissionGroups:function(){var s=$("select[name='permission_group_id']");s.empty();var e=$("input[name='permission_group_type']:checked").val();s.append($("<option/>").val("").text("")),i.permissionGroups.forEach((function(i,t){e===i.type&&s.append($("<option/>").val(i.id).text(i.name))}))}};i.mounted()})();
//# sourceMappingURL=index.bundle.js.map