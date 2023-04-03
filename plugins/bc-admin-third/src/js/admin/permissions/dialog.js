/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * dialog
 * @checked
 * @noTodo
 */
$(function () {

    /**
     * ダイアログを開く
     */
    $("#BtnMenuPermission").click(function () {
        $('#PermissionDialog').dialog('open');
        return false;
    });

    /**
     * バリデーション
     */
    let form = $("#PermissionAjaxAddForm");
    form.validate();
    form.submit(function () {
        return false
    });

    /**
     * ダイアログを初期化
     */
    $("#PermissionDialog").dialog({
        bgiframe: true,
        autoOpen: false,
        width: 'auto',
        modal: true,
        open: function (event, ui) {
            $("#PermissionAjaxAddForm input").first().focus();
        },
        buttons: {
            cancel: {
                text: bcI18n.commonCancel,
                click: function () {
                    $(this).dialog('close');
                }
            },
            save: {
                text: bcI18n.commonSave,
                click: function () {

                    form.submit();
                    if (!form.valid()) return;

                    $.bcToken.check(function () {
                        return $.ajax({
                            url: $.bcUtil.apiAdminBaseUrl + 'baser-core/permissions/add',
                            headers: {
                                "Authorization": $.bcJwt.accessToken,
                            },
                            type: 'POST',
                            data: form.serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                $.bcUtil.hideMessage();
                                $.bcUtil.showLoader();
                            },
                        }).done(function (result) {
                            $.bcUtil.showNoticeMessage(result.message);
                            $("#PermissionDialog").dialog('close');
                        }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
                            $.bcUtil.showAjaxError(bcI18n.commonSaveFailedMessage, XMLHttpRequest, errorThrown);
                        }).always(function(){
                            $.bcUtil.hideLoader();
                        });
                    }, {hideLoader: false});

                }
            }
        }
    });
});
