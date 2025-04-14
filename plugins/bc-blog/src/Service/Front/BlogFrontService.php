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

namespace BcBlog\Service\Front;

use BaserCore\Model\Entity\Content;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Service\BlogCategoriesServiceInterface;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Paging\PaginatedResultSet;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\RedirectException;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

/**
 * BlogFrontService
 *
 * @property BlogPostsService $BlogPostsService
 * @property BlogContentsService $BlogContentsService
 * @property BlogCategoriesService $BlogCategoriesService
 */
class BlogFrontService implements BlogFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * BlogContentsService
     * @var BlogContentsService|BlogContentsServiceInterface
     */
    public BlogContentsService|BlogContentsServiceInterface $BlogContentsService;

    /**
     * BlogPostsService
     * @var BlogPostsService|BlogPostsServiceInterface
     */
    public BlogPostsService|BlogPostsServiceInterface $BlogPostsService;

    /**
     * BlogCategoriesService
     * @var BlogCategoriesService|BlogCategoriesServiceInterface
     */
    public BlogCategoriesService|BlogCategoriesServiceInterface $BlogCategoriesService;

    /**
     * Constructor
     *
     * サービスクラスを初期化する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $this->BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $this->BlogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
    }

    /**
     * 記事一覧用の view 変数を取得する
     *
     * @param ServerRequest $request
     * @return array[]
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getViewVarsForIndex(ServerRequest $request, BlogContent $blogContent, PaginatedResultSet|ResultSet $posts): array
    {
        return [
            'blogContent' => $blogContent,
            'posts' => $posts,
            'single' => false,
            'editLink' => BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogContents',
                'action' => 'edit',
                $blogContent->id
            ] : null,
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * RSS用 の View 変数を取得
     *
     * @param ServerRequest $request
     * @param BlogContent $blogContent
     * @param ResultSet $posts
     * @return array
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndexRss(ServerRequest $request, BlogContent $blogContent, ResultSet $posts): array
    {
        $site = $request->getAttribute('currentSite');
        return [
            'blogContent' => $blogContent,
            'posts' => $posts,
            'channel' => [
                'title' => h(sprintf("%s｜%s", $request->getAttribute('currentContent')->title, $site?->title)),
                'description' => h(strip_tags($blogContent->description))
            ]
        ];
    }

    /**
     * プレビュー用のセットアップをする
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        // ブログコンテンツ取得
        $blogContent = $this->BlogContentsService->get(
            (int)$controller->getRequest()->getAttribute('currentContent')->entity_id
        );
        // ブログコンテンツをPOSTデータにより書き換え
        $blogContent = $this->BlogContentsService->BlogContents->patchEntity(
            $blogContent,
            $controller->getRequest()->getData()
        );
        // ブログコンテンツのアップロードファイルをPOSTデータにより書き換え
        $blogContent->content = new Content($this->BlogContentsService->BlogContents->Contents->saveTmpFiles(
            $controller->getRequest()->getData('content'),
            mt_rand(0, 99999999)
        )->toArray());
        // Request のカレンドコンテンツを書き換え
        $controller->setRequest($controller->getRequest()->withAttribute('currentContent', $blogContent->content));
        /* @var BlogContent $blogContent */
        $controller->set($this->getViewVarsForIndex(
            $controller->getRequest(),
            $blogContent,
            $controller->paginate($this->BlogPostsService->getIndex([
                'blog_content_id' => $blogContent->id,
                'limit' => $blogContent->list_count,
                'status' => 'publish'
            ]))
        ));
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($blogContent));
    }

    /**
     * カテゴリー別アーカイブ一覧の view 変数を取得する
     *
     * @param ResultSet|PaginatedResultSet $posts
     * @param string $category
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByCategory(
        ResultSet|PaginatedResultSet $posts,
        string $category,
        ServerRequest $request,
        EntityInterface $blogContent,
        array $crumbs
    ): array
    {
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $blogCategory = $blogCategoriesTable->find()->where([
            'BlogCategories.blog_content_id' => $blogContent->id,
            'BlogCategories.name' => urlencode($category)
        ])->first();
        if (!$blogCategory) {
            throw new NotFoundException();
        }
        return [
            'posts' => $posts,
            'blogCategory' => $blogCategory,
            'blogContent' => $blogContent,
            'blogArchiveType' => 'category',
            'crumbs' => array_merge($crumbs, $this->getCategoryCrumbs(
                $request->getAttribute('currentContent')->url,
                $blogCategory->id
            )),
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * カテゴリ用のパンくずを取得する
     *
     * @param string $baseUrl
     * @param int $categoryId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCategoryCrumbs(string $baseUrl, int $categoryId, $isCategoryPage = true): array
    {
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $query = $blogCategoriesTable->find('path', for: $categoryId)->select(['name', 'title']);
        $count = $query->count();
        $crumbs = [];
        if ($count <= 1 && $isCategoryPage) return $crumbs;
        foreach($query->all() as $key => $blogCategory) {
            if ($key === ($count - 1) && $isCategoryPage) break;
            $crumbs[] = [
                'name' => $blogCategory->title,
                'url' => sprintf(
                    "%sarchives/category/%s",
                    $baseUrl,
                    $blogCategory->name
                )
            ];
        }
        return $crumbs;
    }

    /**
     * 著者別アーカイブ一覧の view 用変数を取得する
     * @param ResultSet|PaginatedResultSet $posts
     * @param int $userId
     * @param BlogContent $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByAuthor(ResultSet|PaginatedResultSet $posts, int $userId, BlogContent $blogContent): array
    {
        $usersTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $author = $usersTable->find('available')->where(['Users.id' => $userId])->first();
        if (!$author) {
            throw new NotFoundException();
        }
        return [
            'posts' => $posts,
            'blogContent' => $blogContent,
            'blogArchiveType' => 'author',
            'author' => $author,
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * タグ別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet|PaginatedResultSet $posts
     * @param string $tag
     * @param BlogContent $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByTag(ResultSet|PaginatedResultSet $posts, string $tag, BlogContent $blogContent): array
    {
        $tagsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogTags');
        $tag = $tagsTable->find()->where(['name' => urldecode($tag)])->first();
        if (!$blogContent->tag_use || !$tag) throw new NotFoundException();
        return [
            'posts' => $posts,
            'blogContent' => $blogContent,
            'blogArchiveType' => 'tag',
            'blogTag' => $tag,
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * 日付別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet|PaginatedResultSet $posts
     * @param string $year
     * @param string $month
     * @param string $day
     * @param BlogContent $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByDate(
        ResultSet|PaginatedResultSet $posts,
        string $year,
        string $month,
        string $day,
        BlogContent $blogContent
    ): array
    {
        if ($day && $month && $year) {
            $type = 'daily';
        } elseif ($month && $year) {
            $type = 'monthly';
        } elseif ($year) {
            $type = 'yearly';
        } else {
            throw new NotFoundException();
        }

        return [
            'posts' => $posts,
            'blogArchiveType' => $type,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area'),
            'blogContent' => $blogContent
        ];
    }

    /**
     * ブログ記事詳細ページの view 用変数を取得する
     *
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForSingle(ServerRequest $request, EntityInterface $blogContent, array $crumbs): array
    {
        $isPreview = (bool)$request->getQuery('preview');
        $no = $request->getParam('pass.0');
        if (is_string($no)) {
            $no = rawurldecode($no);
        }
        $post = $editLink = null;
        if($isPreview) {
            if($no) {
                $post = $this->BlogPostsService->BlogPosts->getPublishByNo($blogContent->id, $no, true);
            }
        } else {
            if (!$no) throw new NotFoundException();
            $post = $this->BlogPostsService->BlogPosts->getPublishByNo($blogContent->id, $no);
            /* @var BlogPost $post */
            if (!$post) throw new NotFoundException();
            $editLink = BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogPosts',
                'action' => 'edit',
                $post->blog_content_id,
                $post->id
            ] : '';

            // スラッグが設定されている記事にNOでアクセスした場合はリダイレクト
            if ($post->name && $post->name !== $no) {
                $postUrl = $this->BlogPostsService->getUrl($post->blog_content->content, $post, true);
                throw new RedirectException($postUrl);
            }
        }

        // ナビゲーションを設定
        if ($post && $post->blog_category_id) {
            $crumbs = array_merge($crumbs, $this->getCategoryCrumbs(
                $request->getAttribute('currentContent')->url,
                $post->blog_category->id?? null,
                false
            ));
        }

        return [
            'post' => $post,
            'blogContent' => $blogContent,
            'editLink' => $editLink,
            'commentUse' => ($isPreview)? false : $blogContent->comment_use,
            'single' => true,
            'crumbs' => $crumbs,
            'currentWidgetAreaId' => $blogContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForArchives(Controller $controller): void
    {
        // ブログコンテンツ取得
        /* @var BlogContent $blogContent */
        $blogContent = $this->BlogContentsService->get(
            (int)$controller->getRequest()->getAttribute('currentContent')->entity_id
        );
        // view 用編集を取得
        $vars = $this->getViewVarsForSingle(
            $controller->getRequest(),
            $blogContent,
            $controller->viewBuilder()->getVar('crumbs')
        );
        // ブログ記事をPOSTデータにより書き換え
        if ($controller->getRequest()->getData()) {
            $events = BcUtil::offEvent($this->BlogPostsService->BlogPosts->getEventManager(), 'Model.beforeMarshal');
            $request = $controller->getRequest();
            $postArray = $request->getData();
            if ($request->getQuery('preview') === 'draft') {
                $postArray['detail'] = $postArray['detail_draft'];
            }

            $vars['post'] = $this->BlogPostsService->BlogPosts->patchEntity(
                $vars['post'] ?? $this->BlogPostsService->BlogPosts->newEmptyEntity(),
                $this->BlogPostsService->BlogPosts->saveTmpFiles($postArray, mt_rand(0, 99999999))->toArray()
            );

            $validationErrors = $vars['post']->getErrors();
            if ($validationErrors) {
                foreach($validationErrors as $columnsErros) {
                    foreach($columnsErros as $error) {
                        throw new NotFoundException($error);
                    }
                }
            }
            BcUtil::onEvent($this->BlogPostsService->BlogPosts->getEventManager(), 'Model.beforeMarshal', $events);
        }

        $controller->set($vars);
        $controller->viewBuilder()->setTemplate($this->getSingleTemplate($blogContent));
    }

    /**
     * 一覧用のテンプレート名を取得する
     * @param BlogContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'index';
    }

    /**
     * アーカイブページ用のテンプレート名を取得する
     *
     * ブログコンテンツの設定に依存する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getArchivesTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'archives';
    }

    /**
     * ブログ詳細ページ用のテンプレート名を取得する
     *
     * ブログコンテンツの設定に依存する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSingleTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'single';
    }

    /**
     * ブログ投稿者一覧ウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $viewCount
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForBlogAuthorArchivesWidget(int $blogContentId, bool $viewCount)
    {
        try {
            return [
                'blogContent' => $this->BlogContentsService->get($blogContentId),
                'authors' => $this->BlogPostsService->BlogPosts->getAuthors($blogContentId, ['viewCount' => $viewCount])
            ];
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * ブログカレンダーウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param string $year
     * @param string $month
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForBlogCalendarWidget(int $blogContentId, string $year = '', string $month = '')
    {
        $year = h($year);
        $month = h($month);
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        if ($month == 12) {
            $next = $this->BlogPostsService->BlogPosts->existsEntry($blogContentId, $year + 1, 1);
        } else {
            $next = $this->BlogPostsService->BlogPosts->existsEntry($blogContentId, $year, $month + 1);
        }
        if ($month == 1) {
            $prev = $this->BlogPostsService->BlogPosts->existsEntry($blogContentId, $year - 1, 12);
        } else {
            $prev = $this->BlogPostsService->BlogPosts->existsEntry($blogContentId, $year, $month - 1);
        }
        return [
            'blogContent' => $this->BlogContentsService->get($blogContentId),
            'entryDates' => $this->BlogPostsService->BlogPosts->getEntryDates($blogContentId, $year, $month),
            'next' => $next,
            'prev' => $prev
        ];
    }

    /**
     * ブログカテゴリウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $limit
     * @param bool $viewCount
     * @param int $depth
     * @param string|null $contentType
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForBlogCategoryArchivesWidget(
        int $blogContentId,
        bool $limit = false,
        bool $viewCount = false,
        int $depth = 1,
        string $contentType = null
    )
    {
        if ($limit === '0') $limit = false;
        return [
            'blogContent' => $this->BlogContentsService->get($blogContentId),
            'categories' => $this->BlogCategoriesService->BlogCategories->getCategoryList($blogContentId, [
                'type' => $contentType,
                'limit' => $limit,
                'depth' => $depth,
                'viewCount' => $viewCount
            ])
        ];
    }

    /**
     * ブログ年別アーカイブウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param bool $limit
     * @param bool $viewCount
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForBlogYearlyArchivesWidget(
        int $blogContentId,
        bool $limit = false,
        bool $viewCount = false
    )
    {
        return [
            'blogContent' => $this->BlogContentsService->get($blogContentId),
            'postedDates' => $this->BlogPostsService->BlogPosts->getPostedDates($blogContentId, [
                'type' => 'year',
                'limit' => $limit !== '0'? $limit : false,
                'viewCount' => $viewCount
            ])
        ];
    }

    /**
     * ブログ月別アーカイブウィジェット用の View 変数を取得する
     *
     * @param int $blogContentId
     * @param int $limit
     * @param bool $viewCount
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsBlogMonthlyArchivesWidget(
        int $blogContentId,
        int $limit = 12,
        bool $viewCount = false
    )
    {
        return [
            'blogContent' => $this->BlogContentsService->get($blogContentId),
            'postedDates' => $this->BlogPostsService->BlogPosts->getPostedDates($blogContentId, [
                'type' => 'month',
                'limit' => $limit !== 0? $limit : false,
                'viewCount' => $viewCount
            ])
        ];
    }

    /**
     * 最近の投稿ウィジェット用 View 変数を取得する
     * @param int $blogContentId
     * @param int $limit
     * @return array
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsRecentEntriesWidget(int $blogContentId, int $limit = 5)
    {
        $query = $this->BlogPostsService->BlogPosts->find()
            ->where(array_merge(
                ['BlogPosts.blog_content_id' => $blogContentId],
                $this->BlogPostsService->BlogPosts->getConditionAllowPublish()
            ))
            ->orderBy(['BlogPosts.posted DESC']);
        if ($limit) {
            $query->limit($limit);
        }
        return [
            'blogContent' => $this->BlogContentsService->get($blogContentId),
            'recentEntries' => $query->all()
        ];
    }

}
