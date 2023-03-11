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
     * アクセスルールグループ
     */
    permissionGroups: [],

    /**
     * ユーザーグループID
     */
    userGroupId: null,

    /**
     * mounted
     */
    mounted() {
        const $script = $("#AdminPermissionsIndexScript");
        this.userGroupId = $script.attr('data-userGroupId');
        this.permissionGroups =  JSON.parse($script.attr('data-permissionGroups'));
        this.initView();
    },

    /**
     * 表示初期化
     */
    initView() {
        // 並び替え機能実装
        $.bcSortable.init({
            updateSortUrl: $.bcUtil.apiBaseUrl + 'baser-core' + '/permissions/update_sort/' + this.userGroupId + '.json'
        });
        // 一括処理実装
        $.bcBatch.init({
            batchUrl: $.bcUtil.apiBaseUrl + 'baser-core' + '/permissions/batch.json'
        });
        this.initPermissionGroups();
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        console.log('event');
        $("input[name='permission_group_type']").click(this.initPermissionGroups);
    },

    /**
     * パーミッショングループ初期化
     */
    initPermissionGroups() {
        const $permissionGroupId = $("select[name='permission_group_id']");
        $permissionGroupId.empty();
        const type = $("input[name='permission_group_type']:checked").val();

        $permissionGroupId.append(
            $('<option/>')
                .val('')
                .text('')
        );
        permissionGroupsIndex.permissionGroups.forEach(function(value, key){
            if(type !== value.type) return;
            $permissionGroupId.append(
                $('<option/>')
                    .val(value.id)
                    .text(value.name)
            );
        });
    }

}

permissionGroupsIndex.mounted();
