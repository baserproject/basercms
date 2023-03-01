/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){function e(){var e=$("#BtnUpdate"),n=$(".php-notice");$("#php").val()?(e.removeAttr("disabled"),n.hide()):(e.attr("disabled","disabled"),n.show())}$("#BtnUpdate").click((function(){return!!confirm(bcI18n.confirmMessage1)&&($.bcUtil.showLoader(),!0)})),$("#php").change(e),e()}));
//# sourceMappingURL=update.bundle.js.map