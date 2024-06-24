(()=>{"use strict";
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
var t={plugin:null,isWritablePackage:!1,isUpdatable:!1,mounted:function(){var t=$("#AdminPluginsUpdateScript");this.plugin=t.attr("data-plugin"),this.isUpdatable=t.attr("data-isUpdatable"),void 0===this.isUpdatable&&(this.isUpdatable=!1),this.registerEvents(),this.toggleUpdate()},registerEvents:function(){$("#BtnUpdate").on("click",this.update),$("#BtnDownload").on("click",$.bcUtil.showLoader),$("#php").on("change",this.toggleUpdate)},update:function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcUtil.showLoader(),!0)},toggleUpdate:function(){var a=$("#BtnUpdate"),e=$("#BtnDownload"),d=$(".php-notice"),i=$("#php");"BaserCore"===t.plugin?(""!==i.val()?(t.isUpdatable?a.removeAttr("disabled"):a.attr("disabled","disabled"),e.removeAttr("disabled")):(a.attr("disabled","disabled"),e.attr("disabled","disabled")),i.val()?d.hide():d.show()):t.isUpdatable?(a.removeAttr("disabled"),e.removeAttr("disabled")):(a.attr("disabled","disabled"),e.attr("disabled","disabled"))}};t.mounted()})();
//# sourceMappingURL=update.bundle.js.map