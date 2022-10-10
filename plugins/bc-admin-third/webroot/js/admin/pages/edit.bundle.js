/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
$((function(){$("input[type=text]").each((function(){$(this).keypress((function(t){return!t.which||13!==t.which}))})),$("#BtnPreview").click((function(){void 0!==$.bcCkeditor.editor.editor_contents_tmp&&$.bcCkeditor.editor.editor_contents_tmp.execCommand("synchronize")})),$("#BtnSave").click((function(){return void 0!==$.bcCkeditor.editor.editor_contents_tmp&&$.bcCkeditor.editor.editor_contents_tmp.execCommand("synchronize"),$.bcToken.check((function(){$("#PageAdminEditForm").submit()}),{useUpdate:!1,hideLoader:!1}),!1}))}));
//# sourceMappingURL=edit.bundle.js.map