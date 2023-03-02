/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$('input[name="sender_1_"]').click(i),$("#EditForm").click((function(){confirm(bcI18n.confirmMessage2.sprintf($("#form-template").val()))&&($("#edit-layout").val(""),$("#edit-mail-form").val(1),$("#edit-mail").val(""),$("#MailContentAdminEditForm").submit())})),$("#EditMail").click((function(){confirm(bcI18n.confirmMessage3.sprintf($("#mail-template").val()))&&($("#edit-layout").val(""),$("#edit-mail-form").val(""),$("#edit-mail").val(1),$("#MailContentAdminEditForm").submit())}));var e=$("#sender-1");function i(){"1"===$('input[name="sender_1_"]:checked').val()?e.slideDown(100):e.slideUp(100)}e.hide(),void 0===$('input[name="sender_1_"]:checked').val()&&(""!==e.val()?$("#sender-1-1").prop("checked",!0):$("#sender-1-0").prop("checked",!0)),i()}));
//# sourceMappingURL=form.bundle.js.map