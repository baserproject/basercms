/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#BtnSave").click((function(){$.bcUtil.showLoader()})),$("input[name='permission_group_type']").click(i);var e=$("select#permission-group-id").val();function i(){$("select#permission-group-id").val(""),$("select#permission-group-id option").each((function(){""!==$(this).val()&&$(this).remove()}));var e=$("input[name='permission_group_type']:checked").val();JSON.parse($("#permission-group").val()).forEach((function(i){i.type===e&&(name=i.name.replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/'/g,"&#039;").replace(/</g,"&lt;").replace(/>/g,"&gt;"),$("#permission-group-id").append('<option value="'.concat(i.id,'">').concat(name,"</option>")))}))}i(),e&&$("select#permission-group-id").val(e)}));
//# sourceMappingURL=form.bundle.js.map