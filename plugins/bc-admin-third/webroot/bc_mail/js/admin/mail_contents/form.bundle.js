/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$('input[name="sender_1_"]').click(e),$("#EditLayout").click((function(){confirm(bcI18n.confirmMessage1.sprintf($("#layout-template").val()))&&($("#edit-layout").val(1),$("#edit-mail-form").val(""),$("#edit-mail").val(""),$("#MailContentAdminEditForm").submit())})),$("#EditForm").click((function(){confirm(bcI18n.confirmMessage2.sprintf($("#form-template").val()))&&($("#edit-layout").val(""),$("#edit-mail-form").val(1),$("#edit-mail").val(""),$("#MailContentAdminEditForm").submit())})),$("#EditMail").click((function(){confirm(bcI18n.confirmMessage3.sprintf($("#mail-template").val()))&&($("#edit-layout").val(""),$("#edit-mail-form").val(""),$("#edit-mail").val(1),$("#MailContentAdminEditForm").submit())}));var i=$("#sender-1");function e(){"1"===$('input[name="sender_1_"]:checked').val()?i.slideDown(100):i.slideUp(100)}i.hide(),void 0===$('input[name="sender_1_"]:checked').val()&&(""!==i.val()?$("#sender-1-1").prop("checked",!0):$("#sender-1-0").prop("checked",!0)),e()}));
//# sourceMappingURL=form.bundle.js.map