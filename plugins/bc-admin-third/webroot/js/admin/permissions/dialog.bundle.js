/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
$((function(){$("#BtnMenuPermission").click((function(){return $("#PermissionDialog").dialog("open"),!1}));var i=$("#PermissionAjaxAddForm");i.validate(),i.submit((function(){return!1})),$("#PermissionDialog").dialog({bgiframe:!0,autoOpen:!1,width:"auto",modal:!0,open:function(i,e){$("#PermissionAjaxAddForm input").first().focus()},buttons:{cancel:{text:bcI18n.commonCancel,click:function(){$(this).dialog("close")}},save:{text:bcI18n.commonSave,click:function(){i.submit(),i.valid()&&$.bcToken.check((function(){return $.ajax({url:$.bcUtil.apiAdminBaseUrl+"baser-core/permissions/add",type:"POST",data:i.serialize(),dataType:"json",beforeSend:function(){$.bcUtil.hideMessage(),$.bcUtil.showLoader()}}).done((function(i){$.bcUtil.showNoticeMessage(i.message),$("#PermissionDialog").dialog("close")})).fail((function(i,e,o){alert(bcI18n.commonSaveFailedMessage)})).always((function(){$.bcUtil.hideLoader()}))}),{hideLoader:!1})}}}})}));
//# sourceMappingURL=dialog.bundle.js.map