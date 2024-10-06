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
use Cake\Core\Exception\CakeException;
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
     * @return void
     * @checked
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;
        // コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
        // 表示用のコントローラーなのでポストデータのチェックは必要ない
        $this->FormProtection->setConfig('validate', false);
    }

    /**
     * [PUBLIC] ブログを一覧表示する
     *
     * @param BlogFrontService $service
     * @param BlogContentsService $blogContentsService
     * @param BlogPostsService $blogPostsService
     * @return void|ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(
        BlogFrontServiceInterface $service,
        BlogContentsServiceInterface $blogContentsService,
        BlogPostsServiceInterface $blogPostsService)
    {
        $currentContent = $this->getRequest()->getAttribute('currentContent');
        $blogContentId = (int)$currentContent?->entity_id;

        /* @var BlogContent $blogContent */
        $blogContent = $blogContentsService->get($blogContentId, [
            'status' => 'publish',
            'contentId' => $currentContent->id
        ]);

        if ($this->getRequest()->is('rss')) {
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
                'status' => 'publish',
                'draft' => false,
                'contain' => [
                    'Users',
                    'BlogCategories',
                    'BlogContents' => ['Contents'],
                    'BlogComments',
                    'BlogTags',
            ]]));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->getRequest()->is('rss')) {
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
     * - 作成者別記事一覧： /news/archives/author/user-id
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
     * @unitTest
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
                        'direction' => $blogContent->list_direction,
                        'draft' => false
                    ], $this->getRequest()->getQueryParams())), ['limit' => $blogContent->list_count]),
                    $category,
                    $this->getRequest(),
                    $blogContent,
                    $this->viewBuilder()->getVar('crumbs')
                ));
                break;

            case 'author':
                if (count($pass) > 2) $this->notFound();
                $userId = isset($pass[1]) ? (int) $pass[1] : '';
                $this->set($service->getViewVarsForArchivesByAuthor(
                    $this->paginate($blogPostsService->getIndexByAuthor($userId, array_merge([
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction,
                        'draft' => false
                    ], $this->getRequest()->getQueryParams())), ['limit' => $blogContent->list_count]),
                    $userId,
                    $blogContent
                ));
                break;

            case 'tag':
                if (count($pass) > 2) $this->notFound();
                $tag = isset($pass[1])? $pass[1] : '';
                $this->set($service->getViewVarsForArchivesByTag(
                    $this->paginate($blogPostsService->getIndexByTag($tag, array_merge([
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction,
                        'draft' => false
                    ], $this->getRequest()->getQueryParams())), ['limit' => $blogContent->list_count]),
                    $tag,
                    $blogContent
                ));
                break;

            case 'date':
                if (count($pass) > 4) $this->notFound();
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
                    $this->paginate($blogPostsService->getIndexByDate($year, $month, $day, array_merge([
                        'status' => 'publish',
                        'blog_content_id' => $blogContent->id,
                        'direction' => $blogContent->list_direction,
                        'draft' => false
                    ], $this->getRequest()->getQueryParams())), ['limit' => $blogContent->list_count]),
                    $year,
                    $month,
                    $day,
                    $blogContent
                ));
                break;

            default:
                if (count($pass) > 1) $this->notFound();
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
     * 全体タグ一覧
     * @param $name
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
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
                    'message' => $message
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
                'message' => $message
            ]));
        } catch (Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            return $this->response->withStatus(500)->withStringBody(json_encode([
                'message' => $message
            ]));
        }

        try {
            $service->sendCommentToAdmin($entity);
            // コメント承認機能を利用していない場合は、公開されているコメント投稿者に送信
            if (!$blogContent->comment_approve) {
                $service->sendCommentToContributor($entity);
            }
        } catch (CakeException $e) {
            $this->log($e->getMessage());
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
     * @checked
     * @noTodo
     */
    public function captcha(BcCaptchaServiceInterface $service, string $token)
    {
        $this->viewBuilder()->disableAutoLayout();
        $service->render($this->getRequest(), $token);
        exit();
    }

}
