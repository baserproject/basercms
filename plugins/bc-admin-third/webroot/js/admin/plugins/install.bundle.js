/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$(function(){var n=$("#AdminPluginInstallScript").attr("data-resetDbUrl");$("#BtnReset").click(function(){if(!confirm(bcI18n.message1))return!1;$("#AdminPluginInstallForm").attr("action",n),$.bcUtil.showLoader()}),$("#BtnSave").click(function(){$.bcUtil.showLoader()});var i=function(){$("#PermissionAllWarning").toggle("1"===$('input[name="permission"]:checked').val())};$('input[name="permission"]').change(i),i()});
//# sourceMappingURL=install.bundle.js.map