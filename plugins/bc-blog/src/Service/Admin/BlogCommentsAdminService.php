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

use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogCommentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\ORM\ResultSet;

/**
 * BlogCommentsAdminService
 */
class BlogCommentsAdminService extends BlogCommentsService implements BlogCommentsAdminServiceInterface {

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ブログコメント一覧用の view 変数を取得
     *
     * @param int $blogContentId
     * @param int|null $blogPostId
     * @param ResultSet $blogComments
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(int $blogContentId, $blogPostId, ResultSet $blogComments): array
    {
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        return [
            'blogComments' => $blogComments,
            'blogContent' => $blogContentsService->get($blogContentId),
            'blogPost' => ($blogPostId)? $blogPostsService->get($blogPostId) : null
        ];
    }

}
