/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$.bcBatch.init({batchUrl:$.bcUtil.apiAdminBaseUrl+"bc-theme-file/theme_folders/batch.json"}),$("#file").change((function(){$.bcUtil.showLoader(),$("#ThemeFileUpload").submit()}))}));
//# sourceMappingURL=index.bundle.js.map