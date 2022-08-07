/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var t=$("#AdminPluginInstallScript").attr("data-resetDbUrl");$("#BtnReset").click((function(){if(!confirm(bcI18n.message1))return!1;$("#AdminPluginInstallForm").attr("action",t),$.bcUtil.showLoader()})),$("#BtnSave").click((function(){$.bcUtil.showLoader()}))}));
//# sourceMappingURL=install.bundle.js.map