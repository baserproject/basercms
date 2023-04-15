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
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Model\Entity\BlogCategory;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\Datasource\EntityInterface;

/**
 * BlogCategoriesAdminService
 */
class BlogCategoriesAdminService extends BlogCategoriesService implements BlogCategoriesAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ブログカテゴリ一覧用の view 変数取得
     *
     * @param int $blogContentId
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForIndex(int $blogContentId)
    {
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        return [
            'blogContent' => $blogContentsService->get($blogContentId),
            'blogCategories' => $this->getTreeIndex($blogContentId, [])
        ];
    }

    /**
     * ブログカテゴリー登録用の view 変数取得
     *
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForAdd(int $blogContentId, BlogCategory $blogCategory)
    {
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        return [
            'blogContent' => $blogContentsService->get($blogContentId),
            'blogCategory' => $blogCategory,
            'parents' => $this->getControlSource('parent_id', [
                'blogContentId' => $blogContentId
            ])
        ];
    }

    /**
     * ブログカテゴリー編集用の view 変数取得
     *
     * @param int $blogContentId
     * @param BlogCategory|EntityInterface $blogCategory
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForEdit(int $blogContentId, BlogCategory $blogCategory)
    {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogContent = $blogContentsService->get($blogContentId);
        $publishLink = $contentsService->isAllowPublish($blogContent->content)? $contentsService->getUrl(
            sprintf(
                "%s/archives/category/%s",
                rtrim($blogContent->content->url, '/'),
                $blogCategory->name
            ),
            true,
            $blogContent->content->site->use_subdomain
        ) : null;
        return [
            'blogContent' => $blogContent,
            'blogCategory' => $blogCategory,
            'parents' => $this->getControlSource('parent_id', [
                'blogContentId' => $blogContentId,
                'excludeParentId' => $blogCategory->id
            ]),
            'publishLink' => $publishLink
        ];
    }
}
