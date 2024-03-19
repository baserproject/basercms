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
     * 初期のユーザーグループID
     */
    initialUserGroupId: 0,

    /**
     * 初期化
     */
    mounted() {
        this.initialUserGroupId = $('#filter-user-group-id').val();
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $('#filter-user-group-id').change(this.changeList);
        $('input[name="list_type"]').click(this.changeList);
    },

    /**
     * 一覧を初期化
     */
    changeList() {
        $.bcUtil.showLoader();
        let userGroupId = $('#filter-user-group-id').val();
        let listType = $('input[name="list_type"]:checked').val();
        if (permissionGroupsIndex.initialUserGroupId === userGroupId) {
            location.href = `${$.bcUtil.adminBaseUrl}baser-core/permission_groups/index/${userGroupId}?list_type=${listType}`;
        } else {
            location.href = `${$.bcUtil.adminBaseUrl}baser-core/permission_groups/index/${userGroupId}`;
        }
    }

}

permissionGroupsIndex.mounted();
