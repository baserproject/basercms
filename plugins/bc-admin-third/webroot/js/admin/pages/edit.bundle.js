/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
$((function(){$("input[type=text]").each((function(){$(this).keypress((function(e){return!e.which||13!==e.which}))})),$("#BtnSave").click((function(){return"undefined"!=typeof editor_contents_tmp&&editor_contents_tmp.execCommand("synchronize"),$("#PageMode").val("save"),$.bcToken.check((function(){$("#PageAdminEditForm").submit()}),{useUpdate:!1,hideLoader:!1}),!1}))}));
//# sourceMappingURL=edit.bundle.js.map