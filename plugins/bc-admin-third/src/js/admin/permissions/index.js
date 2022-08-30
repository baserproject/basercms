/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {
    const userGroupId = $("#AdminPermissionsIndexScript").attr('data-userGroupId');
    /**
     * 並び替え機能実装
     */
    $.bcSortable.init({
        updateSortUrl: $.bcUtil.apiBaseUrl + 'baser-core' + '/permissions/update_sort/' + userGroupId + '.json'
    });
    /**
     * 一括処理実装
     */
    $.bcBatch.init({
        batchUrl: $.bcUtil.apiBaseUrl + 'baser-core' + '/permissions/batch.json'
    });
});
