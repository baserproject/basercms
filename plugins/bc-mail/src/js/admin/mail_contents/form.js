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
    $('input[name="data[MailContent][sender_1_]"]').click(mailContentSender1ClickHandler);
    $("#MailContentSender1").hide();

    if ($('input[name="data[MailContent][sender_1_]"]:checked').val() === undefined) {
        if ($("#MailContentSender1").val() != '') {
            $("#MailContentSender11").prop('checked', true);
        } else {
            $("#MailContentSender10").prop('checked', true);
        }
    }
    $("#EditLayout").click(function () {
        if (confirm(bcI18n.confirmMessage1.sprintf($("#MailContentLayoutTemplate").val()))) {
            $("#MailContentEditLayout").val(1);
            $("#MailContentEditMailForm").val('');
            $("#MailContentEditMail").val('');
            $("#MailContentAdminEditForm").submit();
        }
    });
    $("#EditForm").click(function () {
        if (confirm(bcI18n.confirmMessage2.sprintf($("#MailContentFormTemplate").val()))) {
            $("#MailContentEditLayout").val('');
            $("#MailContentEditMailForm").val(1);
            $("#MailContentEditMail").val('');
            $("#MailContentAdminEditForm").submit();
        }
    });
    $("#EditMail").click(function () {
        if (confirm(bcI18n.confirmMessage3.sprintf($("#MailContentMailTemplate").val()))) {
            $("#MailContentEditLayout").val('');
            $("#MailContentEditMailForm").val('');
            $("#MailContentEditMail").val(1);
            $("#MailContentAdminEditForm").submit();
        }
    });
    mailContentSender1ClickHandler();
});

function mailContentSender1ClickHandler() {
    if ($('input[name="data[MailContent][sender_1_]"]:checked').val() == '1') {
        $("#MailContentSender1").slideDown(100);
    } else {
        $("#MailContentSender1").slideUp(100);
    }
}
