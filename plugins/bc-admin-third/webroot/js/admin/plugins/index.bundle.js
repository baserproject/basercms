/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$.ajax({url:$.bcUtil.adminBaseUrl+"baser-core/plugins/get_market_plugins",type:"GET",success:function(t){$("#BaserMarket").html(t)}}),$("#tabs").tabs(),$.bcSortable.init({updateSortUrl:$("#AdminPluginsIndexScript").attr("data-updateSortUrl")}),$.bcBatch.init({batchUrl:$("#AdminPluginsIndexScript").attr("data-batchUrl")})}));
//# sourceMappingURL=index.bundle.js.map