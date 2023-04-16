/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

$(function () {

    $("#site-name").focus();

    $('#BtnFinish, #BtnBack').click(function () {
        $.bcUtil.showLoader();
        var result = true;
        if (this.id === 'BtnFinish') {
            $("#mode").val('finish');
            if ($("#site-name").val() === "") {
                alert(bcI18n.message0);
                result = false;
            } else if ($("#admin-email").val() === "") {
                alert(bcI18n.message1);
                result = false;
            } else if ($("#admin-password").val().length < 6) {
                alert(bcI18n.message4);
                result = false;
            } else if ($("#admin-password").val() !== $("#admin-confirm-password").val()) {
                alert(bcI18n.message5);
                result = false;
            } else if (!$("#admin-password").val().match(/^[a-zA-Z0-9\-_ \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*]+$/)) {
                alert(bcI18n.message6);
                result = false;
            }
        } else if (this.id === 'BtnBack') {
            $("#mode").val('back');
        }

        if (result) {
            $('#AdminSettingForm').submit();
        } else {
            $.bcUtil.hideLoader();
            return false;
        }

    });
});
