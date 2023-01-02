/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


$(function () {
    $("#BtnSave").click(function () {
        $.bcUtil.showLoader();
    });

    $("input[name='permission_group_type']").click(updatePermissionGroupId);

    let permissionGroupId = $('select#permission-group-id').val();
    updatePermissionGroupId();
    if (permissionGroupId) {
        $('select#permission-group-id').val(permissionGroupId);
    }

    function updatePermissionGroupId() {
        $('select#permission-group-id').val('');
        $('select#permission-group-id option').each(function () {
            if ($(this).val() !== '') {
                $(this).remove();
            }
        });

        const type = $("input[name='permission_group_type']:checked").val();
        let permissionGroups = JSON.parse($("#permission-group").val());
        permissionGroups.forEach(permissionGroup => {
            if (permissionGroup.type === type) {
                $("#permission-group-id").append(`<option value="${permissionGroup.id}">${permissionGroup.name}</option>`)
            }
        });
    }

});
