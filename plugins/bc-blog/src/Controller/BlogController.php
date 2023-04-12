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

use BaserCore\Error\BcException;
use BaserCore\Service\BcCaptchaServiceInterface;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Service\BlogCommentsServiceInterface;
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
use Cake\ORM\Exception\PersistenceFailedException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

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
        if ($this->getRequest()->getParam('action') === 'index') {
            $this->loadComponent('BaserCore.BcFrontContents');
        } else {
            $this->loadComponent('BaserCore.BcFrontContents', ['viewContentCrumb' => true]);
        }
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;

        $blogContentId = null;

        if ($this->request->getParam('action') !== 'tags') {
            if (!empty($this->request->getParam('entityId'))) {
                $blogContentId = $this->request->getParam('entityId');
            } elseif (!empty($this->request->getParam('pass'))) {
                // 後方互換のため pass もチェック
                $blogContentId = $this->request->getParam('pass');
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
        $this->Security->setConfig('validatePost', false);
    }

    /**
     * [PUBLIC] ブログを一覧表示する
     *
     * @param BlogFrontService $service
     * @param BlogContentsService $blogContentsService
     * @param BlogPostsService $blogPostsService
     * @return void|ResponseInterface
     * @checked
     */
    public function index(
        BlogFrontServiceInterface $service,
        BlogContentsServiceInterface $blogContentsService,
        BlogPostsServiceInterface $blogPostsService)
    {

        $blogContentId = (int)$this->getRequest()->getAttribute('currentContent')->entity_id;

        /* @var BlogContent $blogContent */
        $blogContent = $blogContentsService->get(
            $blogContentId,
            ['status' => 'publish']
        );

        if ($this->RequestHandler->prefers('rss')) {
            $listCount = $blogContent->feed_count;
        } else {
            $listCount = $blogContent->list_count;
        }

        try {
            $this->setRequest($this->getRequest()->withQueryParams(array_merge([
                'limit' => $listCount,
                'sort' => 'BlogPosts.posted',
                'direction' => $blogContent->list_direction
            ], $this->getRequest()->getQueryParams())));
            $entities = $this->paginate($blogPostsService->getIndex([
                'blog_content_id' => $blogContentId,
                'limit' => $listCount,
                'status' => 'publish'
            ]));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->RequestHandler->prefers('rss')) {
            $this->set($service->getViewVarsForIndexRss(
                $this->getRequest(),
                $blogContent,
                $entities
            ));
            $this->render('index');
        } else {
            $this->set($service->getViewVarsForIndex(
                $this->getRequest(),
                $blogContent,
                $entities
            ));
            $this->render($service->getIndexTemplate($blogContent));
        }
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
				if(count($pass) > 2) $this->notFound();
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
				if(count($pass) > 2) $this->notFound();
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
				if(count($pass) > 4) $this->notFound();
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
				if(count($pass) > 1) $this->notFound();
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

    /**
     * ブログコメントを登録する
     *
     * 画像認証を行い認証されればブログのコメントを登録する
     * コメント承認を利用していないブログの場合、公開されているコメント投稿者にアラートを送信する
     */
    public function ajax_add_comment(BlogCommentsServiceInterface $service, BcCaptchaServiceInterface $bcCaptchaService)
    {
        $this->request->allowMethod(['post', 'put']);
        $postData = $this->getRequest()->getData();

        if (!$postData['blog_content_id']) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }
        if (!$postData['blog_post_id']) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_post_id が指定されていません。'));
        }

        $blogContent = $service->getBlogContent($postData['blog_content_id']);
        try {
            if ($blogContent->auth_captcha && !$bcCaptchaService->check(
                $this->getRequest(),
                $this->getRequest()->getData('captcha_id'),
                $this->getRequest()->getData('auth_captcha')
            )) {
                $message = __d('baser_core', '画像の文字が間違っています。再度入力してください。');
                return $this->response->withStatus(400)->withStringBody(json_encode([
                    'message'=> $message
                ]));
            }
            $entity = $service->add($postData['blog_content_id'], $postData['blog_post_id'], $postData);
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser_core', '入力エラーです。内容を見直してください。');
            return $this->response->withStatus(400)->withStringBody(json_encode([
                'message' => $message,
                'errors' => $entity->getErrors()
            ]));
        } catch (BcException $e) {
            $message = $e->getMessage();
            return $this->response->withStatus(400)->withStringBody(json_encode([
                'message'=> $message
            ]));
        } catch (Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            return $this->response->withStatus(500)->withStringBody(json_encode([
                'message'=> $message
            ]));
        }

        $service->sendCommentToAdmin($entity);
        // コメント承認機能を利用していない場合は、公開されているコメント投稿者に送信
        if (!$blogContent->comment_approve) {
            $service->sendCommentToContributor($entity);
        }

        $this->set([
            'blogComment' => $entity ?? null,
        ]);
        $this->viewBuilder()->disableAutoLayout();
        $this->render('element/blog_comment');
    }

    /**
     * 認証用のキャプチャ画像を表示する
     *
     * @return void
     */
    public function captcha(BcCaptchaServiceInterface $service, string $token)
    {
        $this->viewBuilder()->disableAutoLayout();
        $service->render($this->getRequest(), $token);
        exit();
    }

}
