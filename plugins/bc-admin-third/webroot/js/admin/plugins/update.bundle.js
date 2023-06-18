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
var t={plugin:null,mounted:function(){this.plugin=$("#AdminPluginsUpdateScript").attr("data-plugin"),this.registerEvents(),this.toggleUpdate()},registerEvents:function(){$("#BtnUpdate").on("click",this.update),$("#php").on("change",this.toggleUpdate)},update:function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcUtil.showLoader(),!0)},toggleUpdate:function(){var e=$("#BtnUpdate"),n=$(".php-notice");"BaserCore"===t.plugin&&($("#php").val()?(e.removeAttr("disabled"),n.hide()):(e.attr("disabled","disabled"),n.show()))}};t.mounted()})();
//# sourceMappingURL=update.bundle.js.map