/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#AdminSiteConfigsFormScript").attr("data-isAdminSsl");function n(){"BaserCore.BcCkeditor"===$('input[name="editor"]:checked').val()?$(".ckeditor-option").show():$(".ckeditor-option").hide()}$("#BtnSave").click((function(){if(!function(e){return"0"!==e||"1"!==$("input[name='admin_ssl']:checked").val()||($("#SiteConfigSslUrl").val()?($.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage1,defaultCancel:!0,ok:function(){$.bcUtil.showLoader(),$("#SiteConfigFormForm").submit()}}),!1):(alert(bcI18n.alertMessage1),window.location.hash="ssl-url",!1))}(e))return!1;$.bcUtil.showLoader()})),$('input[name="editor"]').click(n),n(),$("#BtnCheckSendmail").click((function(){return!!confirm(bcI18n.confirmMessage2)&&($.bcToken.check((function(){return $.ajax({type:"POST",url:$.bcUtil.apiAdminBaseUrl+"baser-core/site_configs/check_sendmail.json",data:$("#SiteConfigFormForm").serialize(),beforeSend:function(){$("#ResultCheckSendmail").hide(),$("#AjaxLoaderCheckSendmail").show()},success:function(e){$("#ResultCheckSendmail").html(bcI18n.infoMessage1)},error:function(e,n,i){var o="";o=e.responseJSON.message?e.responseJSON.message:i,$("#ResultCheckSendmail").html(bcI18n.alertMessage2+o)},complete:function(){$("#ResultCheckSendmail").show(),$("#AjaxLoaderCheckSendmail").hide()}})}),{loaderType:"none"}),!1)}))}));
//# sourceMappingURL=index.bundle.js.map