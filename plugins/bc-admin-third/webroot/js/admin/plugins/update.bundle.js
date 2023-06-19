(()=>{
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
var t={plugin:null,isWritablePackage:!1,mounted:function(){var t=$("#AdminPluginsUpdateScript");this.plugin=t.attr("data-plugin"),this.isWritablePackage=t.attr("data-isWritablePackage"),this.registerEvents(),this.toggleUpdate()},registerEvents:function(){$("#BtnUpdate").on("click",this.update),$("#php").on("change",this.toggleUpdate)},update:function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcUtil.showLoader(),!0)},toggleUpdate:function(){var e=$("#BtnUpdate"),a=$(".php-notice");"BaserCore"===t.plugin&&($("#php").val()?(e.removeAttr("disabled"),a.hide()):(e.attr("disabled","disabled"),a.show()),t.isWritablePackage||e.attr("disabled","disabled"))}};t.mounted()})();
//# sourceMappingURL=update.bundle.js.map