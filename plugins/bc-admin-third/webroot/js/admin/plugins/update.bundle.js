/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){function t(){var t=$("#BtnUpdate"),e=$(".php-notice");"BaserCore"===$("#AdminPluginsUpdateScript").attr("data-plugin")&&($("#php").val()?(t.removeAttr("disabled"),e.hide()):(t.attr("disabled","disabled"),e.show()))}$("#BtnUpdate").click((function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcUtil.showLoader(),!0)})),$("#php").change(t),t()}));
//# sourceMappingURL=update.bundle.js.map