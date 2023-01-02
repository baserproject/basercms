/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#BtnSave").click((function(){$.bcUtil.showLoader()})),$("input[name='permission_group_type']").click(o);var i=$("select#permission-group-id").val();function o(){$("select#permission-group-id").val(""),$("select#permission-group-id option").each((function(){""!==$(this).val()&&$(this).remove()}));var i=$("input[name='permission_group_type']:checked").val();JSON.parse($("#permission-group").val()).forEach((function(o){o.type===i&&$("#permission-group-id").append('<option value="'.concat(o.id,'">').concat(o.name,"</option>"))}))}o(),i&&$("select#permission-group-id").val(i)}));
//# sourceMappingURL=form.bundle.js.map