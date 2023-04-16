/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$("#EditBlogTemplate").click((function(){confirm(bcI18n.confirmMessage1.sprintf($("#template").val()))&&($("#edit-blog").val(1),$("#BlogContentAdminEditForm").submit())}))}));
//# sourceMappingURL=form.bundle.js.map