/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#BtnNext, #BtnCheckAgain").click((function(){$.bcUtil.showLoader();var c=$("#mode");switch(this.id){case"BtnNext":c.val("next");break;case"BtnCheckAgain":c.val("check")}$("#CheckEnvForm").submit()}))}));
//# sourceMappingURL=step2.bundle.js.map