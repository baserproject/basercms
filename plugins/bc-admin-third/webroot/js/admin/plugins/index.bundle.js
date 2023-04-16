/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$.ajax({url:$.bcUtil.adminBaseUrl+"baser-core/plugins/get_market_plugins",type:"GET",success:function(a){$("#BaserMarket").html(a)}}),$("#tabs").tabs(),$.bcSortable.init({updateSortUrl:$.bcUtil.apiAdminBaseUrl+"baser-core/plugins/update_sort.json"}),$.bcBatch.init({batchUrl:$.bcUtil.apiAdminBaseUrl+"baser-core/plugins/batch.json"})}));
//# sourceMappingURL=index.bundle.js.map