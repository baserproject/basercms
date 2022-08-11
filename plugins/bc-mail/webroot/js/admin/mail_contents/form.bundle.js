(()=>{function n(){"1"==$('input[name="data[MailContent][sender_1_]"]:checked').val()?$("#MailContentSender1").slideDown(100):$("#MailContentSender1").slideUp(100)}
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$('input[name="data[MailContent][sender_1_]"]').click(n),$("#MailContentSender1").hide(),void 0===$('input[name="data[MailContent][sender_1_]"]:checked').val()&&(""!=$("#MailContentSender1").val()?$("#MailContentSender11").prop("checked",!0):$("#MailContentSender10").prop("checked",!0)),$("#EditLayout").click((function(){confirm(bcI18n.confirmMessage1.sprintf($("#MailContentLayoutTemplate").val()))&&($("#MailContentEditLayout").val(1),$("#MailContentEditMailForm").val(""),$("#MailContentEditMail").val(""),$("#MailContentAdminEditForm").submit())})),$("#EditForm").click((function(){confirm(bcI18n.confirmMessage2.sprintf($("#MailContentFormTemplate").val()))&&($("#MailContentEditLayout").val(""),$("#MailContentEditMailForm").val(1),$("#MailContentEditMail").val(""),$("#MailContentAdminEditForm").submit())})),$("#EditMail").click((function(){confirm(bcI18n.confirmMessage3.sprintf($("#MailContentMailTemplate").val()))&&($("#MailContentEditLayout").val(""),$("#MailContentEditMailForm").val(""),$("#MailContentEditMail").val(1),$("#MailContentAdminEditForm").submit())})),n()}))})();
//# sourceMappingURL=form.bundle.js.map