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

    /**
     * 一括処理実装
     */
    $.bcBatch.init({
        batchUrl: $.bcUtil.apiAdminBaseUrl + 'bc-blog/blog_categories/batch.json'
    });

});
