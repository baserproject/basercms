/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


const permissionGroupsIndex = {

    /**
     * 初期化
     */
    mounted() {
        this.registerEvents();
        if($('#filter-user-group-id').val() === '0') {
            $('#list-type-admin').parent().hide();
        }
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $('#filter-user-group-id').change(this.initList);
        $('input[name="list_type"]').click(this.initList);
    },

    /**
     * 一覧を初期化
     */
    initList() {
        $.bcUtil.showLoader();
        let userGroupId = $('#filter-user-group-id').val();
        let listType = $('input[name="list_type"]:checked').val();
        location.href = `${$.bcUtil.adminBaseUrl}baser-core/permission_groups/index/${userGroupId}?list_type=${listType}`;
    }

}

permissionGroupsIndex.mounted();
