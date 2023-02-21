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
use BaserCore\Model\Entity\User;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Vendor\CKEditorStyleParser;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * BlogContentsAdminService
 */
class BlogPostsAdminService extends BlogPostsService implements BlogPostsAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 一覧用の View 変数を取得する
     *
     * @param $posts
     * @param $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex($posts, $request)
    {
        /* @var \BaserCore\Service\UsersService $usersService */
        $usersService = $this->getService(UsersServiceInterface::class);
        /* @var \BaserCore\Service\ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        /* @var \BcBlog\Service\BlogContentsService $blogContentsService */
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

        $publishLink = null;
        if ($contentsService->isAllowPublish($request->getAttribute('currentContent'))) {
            $publishLink = $contentsService->getUrl(
                $request->getAttribute('currentContent')->url,
                true,
                $request->getAttribute('currentSite')->use_subdomain
            );
        }

        return [
            'posts' => $posts,
            'blogContent' => $blogContentsService->get($request->getParam('pass.0')),
            'users' => $usersService->getList(),
            'publishLink' => $publishLink
        ];
    }

    /**
     * 新規登録用の View変数を生成する
     *
     * @param ServerRequest $request
     * @param EntityInterface $post
     * @param User $user
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(ServerRequest $request, EntityInterface $post, EntityInterface $user): array
    {
        /* @var \BcBlog\Service\BlogContentsService $blogContentsService */
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogContent = $blogContentsService->get($request->getParam('pass.0'));
        return [
            'post' => $post,
            'blogContent' => $blogContent,
            'editor' => BcSiteConfig::get('editor'),
            'editorOptions' => $this->getEditorOptions(true),
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br'),
            'users' => $this->BlogPosts->Users->getUserList(),
            'categories' => $this->getControlSource('blog_category_id', [
                'blogContentId' => $blogContent->id,
                'postEditable' => true,
                'empty' => __d('baser', '指定しない')
            ]),
            'hasNewCategoryAddablePermission' => $this->BlogPosts->BlogCategories->hasNewCategoryAddablePermission(
                Hash::extract($user->user_groups, '{n}.id'),
                $blogContent->id
            ),
            'hasNewTagAddablePermission' => $this->BlogPosts->BlogTags->hasNewTagAddablePermission(
                Hash::extract($user->user_groups, '{n}.id'),
                $blogContent->id
            )
        ];
    }

    /**
     * 編集画面用の View変数を生成する
     *
     * @param ServerRequest $request
     * @param EntityInterface|BlogPost $post
     * @param User $user
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(ServerRequest $request, EntityInterface $post, EntityInterface $user): array
    {
        /* @var \BcBlog\Service\BlogContentsService $blogContentsService */
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogContent = $blogContentsService->get($request->getParam('pass.0'));
        return [
            'post' => $post,
            'blogContent' => $blogContent,
            'editor' => BcSiteConfig::get('editor'),
            'editorOptions' => $this->getEditorOptions(false),
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br'),
            'users' => $this->BlogPosts->Users->getUserList(),
            'categories' => $this->getControlSource('blog_category_id', [
                'blogContentId' => $blogContent->id,
                'postEditable' => true,
                'empty' => __d('baser', '指定しない')
            ]),
            'hasNewCategoryAddablePermission' => $this->BlogPosts->BlogCategories->hasNewCategoryAddablePermission(
                Hash::extract($user->user_groups, '{n}.id'),
                $blogContent->id
            ),
            'hasNewTagAddablePermission' => $this->BlogPosts->BlogTags->hasNewTagAddablePermission(
                Hash::extract($user->user_groups, '{n}.id'),
                $blogContent->id
            ),
            'publishLink' => $this->getPublishLink($post)
        ];
    }

    /**
     * 公開ページのリンクを取得する
     *
     * @param BlogPost $post
     * @return string
     * @checked
     * @noTodo
     */
    public function getPublishLink(BlogPost $post)
    {
        if (!$this->allowPublish($post)) return '';
        /* @var \BaserCore\Service\ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        return $contentsService->getUrl(
            sprintf("%sarchives/%s", $post->blog_content->content->url, $post->no),
            true,
            $post->blog_content->content->site->use_subdomain
        );
    }

    /**
     * CKEditorのオプションを取得する
     *
     * @param bool $isDisableDraft
     * @return array|array[]|string[]
     * @checked
     * @noTodo
     */
    public function getEditorOptions(bool $isDisableDraft)
    {
        $editorOptions = ['editorDisableDraft' => $isDisableDraft];
        if (BcSiteConfig::get('editor_styles')) {
            $ckEditorStyleParser = new CKEditorStyleParser();
            $editorStyles = [
                'default' => $ckEditorStyleParser->parse(BcSiteConfig::get('editor_styles'))
            ];
            $editorOptions = array_merge($editorOptions, [
                'editorStylesSet' => 'default',
                'editorStyles' => $editorStyles
            ]);
        }
        return $editorOptions;
    }

}
