<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBlog\Service\Admin;

use Cake\ORM\ResultSet;

/**
 * BlogCommentsAdminServiceInterface
 */
interface BlogCommentsAdminServiceInterface
{

    /**
     * ブログコメント一覧用の view 変数を取得
     *
     * @param int $blogContentId
     * @param int $blogPostId
     * @param ResultSet $blogComments
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(int $blogContentId, int $blogPostId, ResultSet $blogComments): array;

}
