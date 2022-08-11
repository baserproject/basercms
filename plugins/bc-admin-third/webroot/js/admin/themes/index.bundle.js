/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$.baserAjaxDataList.config.methods.copy={button:".btn-copy",confirm:"",result:function(t,a){var e=$.baserAjaxDataList.config;a?($.bcUtil.showLoader(),document.location.reload()):($(e.alertBox).html(bcI18n.alertMessage1),$(e.alertBox).fadeIn(500))}},$.baserAjaxDataList.config.methods.del={button:".btn-delete",confirm:bcI18n.confirmMessage1,result:function(t,a){var e=$.baserAjaxDataList.config;a?($.bcUtil.showLoader(),document.location.reload()):($(e.alertBox).html(bcI18n.alertMessage2),$(e.alertBox).fadeIn(500))}},$.baserAjaxDataList.init(),$("#BtnLoadDefaultDataPattern").click((function(){return $.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage2,ok:function(){$.bcUtil.showLoader(),$("#ThemeLoadDefaultDataPatternForm").submit()}}),!1})),$.ajax({url:$.baseUrl()+"/"+$.bcUtil.adminPrefix+"/themes/ajax_get_market_themes",type:"GET",success:function(t){$("#BaserMarket").html(t)}}),$("#tabs").tabs()}));
//# sourceMappingURL=index.bundle.js.map