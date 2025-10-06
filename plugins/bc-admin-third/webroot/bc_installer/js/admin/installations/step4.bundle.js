/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
$(function(){$("#site-name").focus(),$("#BtnFinish, #BtnBack").click(function(){$.bcUtil.showLoader();var i=!0;if("BtnFinish"===this.id?($("#mode").val("finish"),""===$("#site-name").val()?(alert(bcI18n.message0),i=!1):""===$("#admin-email").val()?(alert(bcI18n.message1),i=!1):""!==$("#admin-password").val()&&""!==$("#admin-confirm-password").val()||(alert(bcI18n.message2),i=!1)):"BtnBack"===this.id&&$("#mode").val("back"),!i)return $.bcUtil.hideLoader(),!1;$("#AdminSettingForm").submit()})});
//# sourceMappingURL=step4.bundle.js.map