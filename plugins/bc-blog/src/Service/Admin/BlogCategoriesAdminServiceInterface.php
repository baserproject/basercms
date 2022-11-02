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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcBlog\Model\Entity\BlogCategory;

/**
 * BlogCategoriesAdminServiceInterface
 */
interface BlogCategoriesAdminServiceInterface
{

    /**
     * ブログカテゴリ一覧用の view 変数取得
     * 
     * @param int $blogContentId
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForIndex(int $blogContentId);

    /**
     * ブログカテゴリー登録用の view 変数取得
     * 
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForAdd(int $blogContentId, BlogCategory $blogCategory);

    /**
     * ブログカテゴリー編集用の view 変数取得
     * 
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForEdit(int $blogContentId, BlogCategory $blogCategory);

}
