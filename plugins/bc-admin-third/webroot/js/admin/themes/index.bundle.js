/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#tabs").tabs(),$(".theme-popup").colorbox({inline:!0,width:"60%"}),$("#BtnLoadDefaultDataPattern").click((function(){return $.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage1,ok:function(){$.bcUtil.showLoader(),$("#ThemeLoadDefaultDataPatternForm").submit()}}),!1})),$.ajax({url:$.bcUtil.adminBaseUrl+"baser-core/themes/get_market_themes",type:"GET",success:function(t){$("#BaserMarket").html(t)}})}));
//# sourceMappingURL=index.bundle.js.map