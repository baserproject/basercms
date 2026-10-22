/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$(function(){function e(){"BaserCore.BcCkeditor"===$('input[name="editor"]:checked').val()?$(".ckeditor-option").show():$(".ckeditor-option").hide()}$("#BtnSave").click(function(){$.bcUtil.showLoader()}),$('input[name="editor"]').click(e),e(),$("#BtnCheckSendmail").click(function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcToken.check(function(){return $.ajax({type:"POST",url:$.bcUtil.apiAdminBaseUrl+"baser-core/site_configs/check_sendmail.json",data:$("#SiteConfigFormForm").serialize(),beforeSend:function(){$("#ResultCheckSendmail").hide(),$("#AjaxLoaderCheckSendmail").show()},success:function(e){$("#ResultCheckSendmail").html(bcI18n.infoMessage1)},error:function(e,n,c){var i=$.bcUtil.getResponseMessage(e);i||(i=$.bcUtil.escapeHtml(c)),$("#ResultCheckSendmail").html(bcI18n.alertMessage1+i)},complete:function(){$("#ResultCheckSendmail").show(),$("#AjaxLoaderCheckSendmail").hide()}})},{loaderType:"none"}),!1)})});
//# sourceMappingURL=index.bundle.js.map