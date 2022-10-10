/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */


$(function () {
    $("input[type=text]").each(function () {
        $(this).keypress(function (e) {
            if (e.which && e.which === 13) {
                return false;
            }
            return true;
        });
    });
    $("#BtnPreview").click(function (){
        if (typeof $.bcCkeditor.editor.editor_contents_tmp !== "undefined") {
            $.bcCkeditor.editor.editor_contents_tmp.execCommand('synchronize');
        }
    });
    $("#BtnSave").click(function () {
        if (typeof $.bcCkeditor.editor.editor_contents_tmp !== "undefined") {
            $.bcCkeditor.editor.editor_contents_tmp.execCommand('synchronize');
        }
        $.bcToken.check(function () {
            $("#PageAdminEditForm").submit();
        }, {useUpdate: false, hideLoader: false});
        return false;
    });
});
