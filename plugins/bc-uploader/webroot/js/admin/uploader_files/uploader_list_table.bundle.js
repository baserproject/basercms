/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#FileList").bind("loadTableComplete",(function(){$(".btn-delete").click((function(){return confirm(bcI18n.uploaderConfirmMessage1)&&$.bcToken.submitToken($(this).attr("href")),!1}))}))}));
//# sourceMappingURL=uploader_list_table.bundle.js.map