/*! For license information please see install.bundle.js.LICENSE.txt */
$((function(){var t=$("#AdminPluginInstallScript").attr("data-resetDbUrl");$("#BtnReset").click((function(){if(!confirm(bcI18n.message1))return!1;$("#AdminPluginInstallForm").attr("action",t),$.bcUtil.showLoader()})),$("#BtnSave").click((function(){$.bcUtil.showLoader()}))}));
//# sourceMappingURL=install.bundle.js.map