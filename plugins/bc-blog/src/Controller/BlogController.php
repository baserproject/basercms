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

namespace BcBlog\Controller;

use BcBlog\Model\Entity\BlogContent;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\Front\BlogFrontService;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Http\Exception\NotFoundException;

/**
 * ブログ記事コントローラー
 */
class BlogController extends BlogFrontAppController
{

    /**
     * initialize
     *
     * コンポーネントをロードする
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents', ['isContentsPage' => false]);
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $blogContentId = null;

        if ($this->request->getParam('action') !== 'tags') {
            if (!empty($this->request->getParam('entityId'))) {
                $blogContentId = $this->request->getParam('entityId');
            } elseif (!empty($this->request->getParam('pass'))) {
                // 後方互換のため pass もチェック
                $blogContentId = $this->request->getParam('pass');
            }
            if (!$blogContentId) {
                $this->notFound();
            }
        }

        if (empty($this->request->getAttribute('currentContent'))) {
            // ウィジェット系の際にコンテンツ管理上のURLでないので自動取得できない
            $content = $this->BcContents->getContent($blogContentId);
            if ($content) {
                $this->request = $this->request->withParam('Content', $content['Content']);
                $this->request = $this->request->withParam('Site', $content['Site']);
            }
        }

        // TODO ucmitz 未検証
        /* >>>
        if (!empty($this->blogContent['BlogContent']['id'])) {
            $this->BlogPost->setupUpload($this->blogContent['BlogContent']['id']);
        }

        // ページネーションのリンク対策
        // コンテンツ名を変更している際、以下の設定を行わないとプラグイン名がURLに付加されてしまう
        // Viewで $paginator->options = array('url' => $this->passedArgs) を行う事が前提
        if (!empty($this->request->getAttribute('currentContent'))) {
            $this->passedArgs['controller'] = $this->request->getAttribute('currentContent')->name;
            $this->passedArgs['plugin'] = $this->request->getAttribute('currentContent')->name;
        }
        $this->passedArgs['action'] = $this->action;
        <<< */

        // コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
        // 表示用のコントローラーなのでポストデータのチェックは必要ない
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    /**
     * [PUBLIC] ブログを一覧表示する
     *
     * @param BlogFrontService $service
     * @param BlogContentsService $blogContentsService
     * @param BlogPostsService $blogPostsService
     * @checked
     */
    public function index(
        BlogFrontServiceInterface $service,
        BlogContentsServiceInterface $blogContentsService,
        BlogPostsServiceInterface $blogPostsService)
    {
        // TODO ucmitz 未検証
//        if ($this->BcContents->preview === 'default' && $this->request->getData()) {
//            $this->blogContent['BlogContent'] = $this->request->getData('BlogContent');
//            $this->request = $this->request->withParsedBody($this->Content->saveTmpFiles(
//                $this->request->getData(), mt_rand(0, 99999999)
//            ));
//            $this->request->withParam('Content.eyecatch', $this->request->getData('Content.eyecatch'));
//        }

        if ($this->RequestHandler->prefers('rss')) {
//            Configure::write('debug', 0);
//            if ($this->blogContent) {
//                $channel = [
//                    'title' => h(
//                        sprintf(
//                            "%s｜%s",
//                            $this->request->getAttribute('currentContent')->title, $this->siteConfigs['name']
//                        )
//                    ),
//                    'description' => h(strip_tags($this->blogContent['BlogContent']['description']))
//                ];
//                $listCount = $this->blogContent['BlogContent']['feed_count'];
//            } else {
//                $channel = [
//                    'title' => $this->siteConfigs['name'],
//                    'description' => $this->siteConfigs['description']
//                ];
//                // TODO 暫定的に一番最初に登録したブログコンテンツの表示件数を利用
//                // BlogConfig で設定できるようにする
//                $blogContent = $this->BlogContent->find(
//                    'first',
//                    ['order' => 'BlogContent.id', 'recirsive' => -1]
//                );
//                $listCount = $blogContent['BlogContent']['feed_count'];
//                $this->blogContent = $blogContent;
//            }
//            $this->set('channel', $channel);
//            $this->layout = 'default';
//            $template = 'index';
        }

        /* @var BlogContent $blogContent */
        $blogContent = $blogContentsService->get(
            (int)$this->getRequest()->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );

        try {
            $entities = $this->paginate($blogPostsService->getIndex([
                'limit' => $blogContent->list_count,
                'status' => 'publish'
            ]));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }

        $this->set($service->getViewVarsForIndex(
            $this->getRequest(),
            $blogContent,
            $entities
        ));
        $this->render($service->getIndexTemplate($blogContent));
    }

    /**
     * [PUBLIC] ブログアーカイブを表示する
     * 
     * $type として、category / author / tag / date を指定し、ブログ記事をそれぞれのタイプごとにフィルタリングする事ができる。
     * また、$type を指定しない場合は、詳細ページを表示する。
     * 
     * ### URL例
     * - カテゴリ別記事一覧： /news/archives/category/category-name
     * - 作成者別記事一覧： /news/archives/author/author-name
     * - タグ別記事一覧： /news/archives/tag/tag-name
     * - 年別記事一覧： /news/archives/date/2022
     * - 月別記事一覧： /news/archives/date/2022/12
     * - 日別記事一覧： /news/archives/date/2022/12/12
     * - 詳細ページ：/news/archives/1
     *
     * @param BlogFrontService $service
     * @param BlogContentsService $blogContentsService
     * @param BlogPostsService $blogPostsService
     * @param string $type
     * @return void
     * @checked
     * @noTodo
     */
    public function archives(
        BlogFrontServiceInterface $service,
        BlogContentsServiceInterface $blogContentsService,
        BlogPostsServiceInterface $blogPostsService,
        string $type
    )
    {
        /* @var BlogContent $blogContent */
        $blogContent = $blogContentsService->get(
            $this->getRequest()->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );
        $pass = $this->getRequest()->getParam('pass');

        switch($type) {
            case 'category':
                $category = $pass[count($pass) - 1];
                $this->set($service->getViewVarsForArchivesByCategory(
                    $this->paginate($blogPostsService->getIndexByCategory($category, array_merge([
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction
                    ], $this->getRequest()->getQueryParams()))),
                    $category,
                    $this->getRequest(),
                    $blogContent,
                    $this->viewBuilder()->getVar('crumbs')
                ));
                break;

            case 'author':
                $author = isset($pass[1])? $pass[1] : '';
                $this->set($service->getViewVarsForArchivesByAuthor(
                    $this->paginate($blogPostsService->getIndexByAuthor($author, [
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction
                    ])),
                    $author,
                    $blogContent
                ));
                break;

            case 'tag':
                $tag = isset($pass[1])? $pass[1] : '';
                $this->set($service->getViewVarsForArchivesByTag(
                    $this->paginate($blogPostsService->getIndexByTag($tag, [
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction
                    ])),
                    $tag,
                    $blogContent
                ));
                break;

            case 'date':
                $year = $month = $day = '';
                if (isset($pass[1]) && preg_match('/^\d{4}$/', $pass[1])) {
                    $year = $pass[1];
                    if ($year && isset($pass[2]) && preg_match('/^((0?[1-9])|(1[0-2]))$/', $pass[2])) {
                        $month = $pass[2];
                        if ($month && isset($pass[3]) && preg_match('/^((0?[1-9])|([1-2][0-9])|(3[0-1]))$/', $pass[3])) {
                            $day = $pass[3];
                        }
                    }
                }
                $this->set($service->getViewVarsForArchivesByDate(
                    $this->paginate($blogPostsService->getIndexByDate($year, $month, $day, [
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction
                    ])),
                    $year,
                    $month,
                    $day,
                    $blogContent
                ));
                break;

            default:
                $this->set($service->getViewVarsForSingle(
                    $this->getRequest(),
                    $blogContent,
                    $this->viewBuilder()->getVar('crumbs')
                ));
        }

        if (in_array($type, ['category', 'author', 'tag', 'date'])) {
            $template = $service->getArchivesTemplate($blogContent);
        } else {
            $template = $service->getSingleTemplate($blogContent);
        }
        $this->render($template);
    }

    /**
     * 記事リストを出力
     * requestAction用
     *
     * @param int $blogContentId
     * @param mixed $num
     */
    public function posts($blogContentId, $limit = 5)
    {
        if (!empty($this->request->getParam('named.template'))) {
            $template = $this->request->getParam('named.template');
        } else {
            $template = 'posts';
        }

        $this->request->withParam('named.template', null);

        $this->layout = null;
        $this->contentId = $blogContentId;

        $datas = $this->_getBlogPosts(['num' => $limit]);

        $this->set('posts', $datas);

        $this->render($this->blogContent['BlogContent']['template'] . DS . $template);
    }

    /**
     * 全体タグ一覧
     * @param $name
     * @checked
     * @noTodo
     */
    public function tags(BlogPostsServiceInterface $service, $name = null)
    {
        if (empty($name)) $this->notFound();
        $this->setViewConditions([], [
            'default' => ['query' => ['limit' => 10]]
        ]);
        $params = array_merge($this->request->getQueryParams(), ['status' => 'publish']);
        try {
            $entities = $this->paginate($service->getIndex(array_merge(['tag' => $name], $params)));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'search']);
        }
        $this->set([
            'posts' => $entities,
            'tag' => rawurldecode($name)
        ]);
    }
}
