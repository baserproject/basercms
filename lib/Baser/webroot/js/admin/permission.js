/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

$(function(){

	$("#BtnMenuPermission").click(function(){
		$("#PermissionDialog").dialog('option', 'position', { my: "center", at: "center", of: window });
		$('#PermissionDialog').dialog('open');
		return false;
	});
	/**
	 * バリデーション
	 */
	$("#PermissionAjaxAddForm").validate();
	$("#PermissionAjaxAddForm").submit(function(){return false});

	/**
	 * ダイアログを初期化
	 */
	$("#PermissionDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		position: [250, 150],
		width: 'auto',
		modal: true,
		open: function(event, ui){
			$("#PermissionName").val($("#CurrentPageName").html());
			$("#PermissionUrl").val($("#CurrentPageUrl").html().replace($("#PermissionAdmin").html(), ''));
			$("#PermissionAjaxAddForm").submit();
			$("#PermissionName").focus();
		},
		close: function() {
			$("#PermissionName").val('');
			$("#PermissionUrl").val('');
		},
		buttons: {
			cancel: {
				text: bcI18n.commonCancel,
				click: function() {
					$(this).dialog('close');
				}
			},
			save: {
				text: bcI18n.commonSave,
				click:  function() {
					$("#PermissionAjaxAddForm").submit();
					if($("#PermissionAjaxAddForm").valid()) {
						$("#PermissionAjaxAddForm").ajaxSubmit({
							beforeSend: function() {
								$("#Waiting").show();
							},
							success: function(response, status) {
								if(response) {
									$("#PermissionDialog").dialog('close');
								} else {
									alert(bcI18n.commonSaveFailedMessage);
								}
							},
							error: function() {
								alert(bcI18n.commonSaveFailedMessage);
							},
							complete: function(){
								$("#Waiting").hide();
							}
						});
					}
				}
			}
		}
	});
});