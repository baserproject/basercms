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

namespace BcBlog\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcBlog\Service\Admin\BlogPostsAdminService;
use BcBlog\Service\Admin\BlogPostsAdminServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\Exception\NotFoundException;

/**
 * ブログ記事コントローラー
 */
class BlogPostsController extends BlogAdminAppController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * initialize
     *
     * コンテンツ管理用のコンポーネントをロードする。
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', ['entityVarName' => 'blogContent']);
    }

    /**
     * beforeFilter
     *
     * 初期セットアップとして次を実施
     * - ServerRequest インスタンスに現在のコンテンツデータをセット
     * - BlogPostsTable のファイルアップロードの設定を実施
     * - エディタ用のヘルパーをセット
     *
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;

        $blogContentId = $this->request->getParam('pass.0');
        if (!$blogContentId) throw new BcException(__d('baser_core', '不正なURLです。'));

        /* @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $request = $contentsService->setCurrentToRequest(
            'BcBlog.BlogContent',
            $blogContentId,
            $this->getRequest()
        );

        if (!$request) throw new BcException(__d('baser_core', 'コンテンツデータが見つかりません。'));

        $this->setRequest($request);

        /* @var BlogPostsService $service */
        $service = $this->getService(BlogPostsServiceInterface::class);
        $service->setupUpload($blogContentId);

        if (BcSiteConfig::get('editor') && BcSiteConfig::get('editor') !== 'none') {
            $this->viewBuilder()->addHelpers([BcSiteConfig::get('editor')]);
        }
    }

    /**
     * [ADMIN] ブログ記事一覧表示
     *
     * ブログ記事の一覧を表示する。
     * ページネーションで次の記事が見つからなかった場合は、１ページ目にリダイレクトする。
     *
     * @param BlogPostsAdminServiceInterface $service
     * @param int $blogContentId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogPostsAdminServiceInterface $service, int $blogContentId)
    {
        $this->setViewConditions('BlogPost', [
            'group' => $blogContentId,
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
                    'sort' => 'no',
                    'direction' => 'desc',
                ]]]);

        // EVENT BlogPosts.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->getRequest()
        ]);
        if ($event !== false) {
            $this->setRequest(($event->getResult() === null || $event->getResult() === true) ? $event->getData('request') : $event->getResult());
        }

        try {
            $this->paginate = [
                'sortableFields' => [
                    'no', 'name','BlogCategories.name','user_id','posted'
                ]
            ];
            $entities = $this->paginate($service->getIndex(array_merge(
                ['blog_content_id' => $blogContentId],
                $this->getRequest()->getQueryParams()
            )));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index', $blogContentId]);
        }

        /* @var BlogPostsAdminService $service */
        $this->set($service->getViewVarsForIndex($entities, $this->getRequest()));
        $this->setRequest($this->getRequest()->withParsedBody($this->getRequest()->getQueryParams()));
    }

    /**
     * [ADMIN] ブログ記事追加処理
     *
     * 指定したブログに記事を追加して、ブログ記事編集画面へリダイレクトする。
     *
     * ###エラー
     * ブログ記事の追加に失敗した場合、PersistenceFailedExceptionかBcExceptionが発生する。
     *
     * @param BlogPostsService $service
     * @param int $blogContentId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogPostsAdminServiceInterface $service, int $blogContentId)
    {
        if ($this->request->is(['post', 'put'])) {
            // EVENT BlogPosts.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $post = $service->create($this->request->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', '記事「%s」を追加しました。'), $post->title));
                // EVENT BlogPosts.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $post
                ]);
                $this->redirect(['action' => 'edit', $blogContentId, $post->id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $post = $e->getEntity();
                // 入力時アイキャッチの配列問題で表示がエラーとなるため、$this->request->data は空にする
                $this->setRequest($this->getRequest()->withParsedBody([]));
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (BcException $e) {
                if ($e->getCode() === "23000") {
                    $this->BcMessage->setError(__d('baser_core', '同時更新エラーです。しばらく経ってから保存してください。'));
                } else {
                    $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
                }
            }
        }
        $user = BcUtil::loginUser();
        $this->set($service->getViewVarsForAdd(
            $this->getRequest(),
            $post ?? $service->getNew($blogContentId, $user->id),
            $user
        ));
    }

    /**
     * [ADMIN] ブログ記事編集処理
     *
     * 指定したブログ記事を編集する。
     * 記事の保存に失敗した場合、PersistenceFailedExceptionかBcExceptionのエラーが発生する。
     *
     * @param BlogPostsService $service
     * @param int $blogContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogPostsAdminServiceInterface $service, int $blogContentId, int $id)
    {
        $post = $service->get($id);
        if ($this->request->is(['post', 'put'])) {
            // EVENT BlogPosts.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                // データを保存
                $post = $service->update($post, $this->request->getData());
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', '記事「%s」を更新しました。'), $post->title));
                // EVENT BlogPosts.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $post
                ]);
                $this->redirect(['action' => 'edit', $blogContentId, $id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $post = $e->getEntity();
                // 入力時アイキャッチの配列問題で表示がエラーとなるため、$this->request->data は空にする
                $this->setRequest($this->getRequest()->withParsedBody([]));
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (BcException $e) {
                if ($e->getCode() === "23000") {
                    $this->BcMessage->setError(__d('baser_core', '同時更新エラーです。しばらく経ってから保存してください。'));
                } else {
                    $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
                }
            }
        }
        $this->set($service->getViewVarsForEdit($this->getRequest(), $post, BcUtil::loginUser()));
    }

    /**
     * [ADMIN] ブログ記事削除処理
     *
     * 指定したブログ記事を削除し、ブログ記事一覧へリダイレクトする。
     *
     * @param BlogPostsServiceInterface $service
     * @param int $blogContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogPostsServiceInterface $service, int $blogContentId, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $blogPost = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'ブログ記事「{0}」を削除しました。', $blogPost->title));
            }
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }

        return $this->redirect(['action' => 'index', $blogContentId]);
    }

    /**
     * [ADMIN] ブログ記事を非公開状態にする
     *
     * 指定したブログ記事を非公開にしてブログ記事一覧へリダイレクトする。
     *
     * @param string $blogContentId
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(BlogPostsServiceInterface $service, $blogContentId, $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            /* @var BlogPostsService $service */
            $result = $service->unpublish($id);
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'ブログ記事「%s」を非公開状態にしました。'), $result->title));
            } else {
                $this->BcMessage->setSuccess(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        return $this->redirect(['action' => 'index', $blogContentId]);
    }

    /**
     * [ADMIN] ブログ記事を公開状態にする
     *
     * 指定したブログ記事を公開状態にしてブログ記事一覧にリダイレクトする。
     *
     * @param string $blogContentId
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(BlogPostsServiceInterface $service, $blogContentId, $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                /* @var BlogPostsService $service */
                $result = $service->publish($id);
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'ブログ記事「%s」を公開状態にしました。'), $result->title));
            } catch (BcException $e) {
                $this->BcMessage->setSuccess(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        return $this->redirect(['action' => 'index', $blogContentId]);
    }

    /**
     * [ADMIN] コピー
     *
     * 指定したブログ記事をコピーする。
     * HTTPメソッドがGETの場合はコピー処理は行わず、ブログ記事一覧へリダイレクトする。
     *
     * @param BlogPostsServiceInterface $service
     * @param int $blogContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(BlogPostsServiceInterface $service, $blogContentId, $id = null)
    {
        $post = $service->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $service->copy($id);
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'ブログ記事「%s」をコピーしました。'), $post->title));
                return $this->redirect(['action' => 'index', $blogContentId]);

            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }
        return $this->redirect(['action' => 'index', $blogContentId]);
    }
}
