/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
$((function(){$("#site-name").focus(),$("#BtnFinish, #BtnBack").click((function(){$.bcUtil.showLoader();var a=!0;if("BtnFinish"===this.id?($("#mode").val("finish"),""===$("#site-name").val()?(alert(bcI18n.message0),a=!1):""===$("#admin-email").val()?(alert(bcI18n.message1),a=!1):$("#admin-password").val().length<6?(alert(bcI18n.message4),a=!1):$("#admin-password").val()!==$("#admin-confirm-password").val()?(alert(bcI18n.message5),a=!1):$("#admin-password").val().match(/^[a-zA-Z0-9\-_ \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*]+$/)||(alert(bcI18n.message6),a=!1)):"BtnBack"===this.id&&$("#mode").val("back"),!a)return $.bcUtil.hideLoader(),!1;$("#AdminSettingForm").submit()}))}));
//# sourceMappingURL=step4.bundle.js.map