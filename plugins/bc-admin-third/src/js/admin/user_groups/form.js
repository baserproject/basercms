/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


/**
 * userGroupsForm
 */
const userGroupsForm = {

    /**
     * 初期化
     */
    mounted() {
        this.initView();
    },

    /**
     * 表示初期化
     */
    initView() {
        if ($("#AdminUserGroupsFormScript").attr('data-isAdmin')) {
            $("#auth_prefix_Admin").prop('disabled', true);
        }
        this.initAuthPrefixSettings();
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#UserGroupAdminEditForm, #UserGroupAdminAddForm").submit(function () {
            $("#auth_prefix_Admin").removeAttr('disabled');
            $("input[name='auth_prefix[]']").each(function(){
                $("input[name='auth_prefix_settings[" + $(this).val() + "][type]']")
                    .removeAttr('disabled');
            });
        });
        $("input[name='auth_prefix[]']").click(this.initAuthPrefixSettings)
    },

    /**
     * 認証プレックス初期化
     */
    initAuthPrefixSettings() {
        $("input[name='auth_prefix[]']").each(function(){
            if(!$(this).prop('checked')) {
                $("input[name='auth_prefix_settings[" + $(this).val() + "][type]']")
                    .prop('disabled', true);
            } else {
                $("input[name='auth_prefix_settings[" + $(this).val() + "][type]']")
                    .prop('disabled', false);
            }
        });
    }

};

userGroupsForm.mounted();




