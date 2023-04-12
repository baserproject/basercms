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
namespace BcBlog\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogPostsController
 */
class BlogPostsController extends BcApiController
{

    /**
     * [API] ブログ記事一覧データ取得
     *
     * @param BlogPostsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogPostsServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'contain' => null,
            'status' => 'publish'
        ], $queryParams);
        $this->set([
            'blogPosts' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogPosts']);
    }

    /**
     * [API] ブログ記事単一データ取得
     *
     * クエリーパラーメーター
     * - status: string 公開ステータス（初期値：publish）
     *  - `publish` 公開されたページ
     *  - `` 全て
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogPost = $message = null;
        try {
            $blogPost = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'blogPost' => $blogPost,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogPost', 'message']);
    }

}
