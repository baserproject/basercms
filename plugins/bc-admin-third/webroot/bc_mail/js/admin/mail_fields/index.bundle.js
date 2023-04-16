/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){var i=$("#AdminMailFieldsIndexScript").attr("data-mailContentId");$.bcSortable.init({updateSortUrl:$.bcUtil.apiAdminBaseUrl+"bc-mail/mail_fields/update_sort/"+i+".json"}),$.bcBatch.init({batchUrl:$.bcUtil.apiAdminBaseUrl+"bc-mail/mail_fields/batch.json"})}));
//# sourceMappingURL=index.bundle.js.map