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
use BaserCore\Error\BcException;
use BcBlog\Service\BlogCategoriesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogCategoriesController
 */
class BlogCategoriesController extends BcApiController
{

    /**
     * [API] ブログカテゴリー一覧取得
     *
     * @param BlogCategoriesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }


        if (!isset($queryParams['blog_content_id']) || empty($queryParams['blog_content_id'])) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogCategories = $message = null;
        try {
            $blogCategories = $this->paginate($service->getIndex($queryParams['blog_content_id'], $queryParams));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogCategories' => $blogCategories,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogCategories', 'message']);
    }

    /**
     * [API] 単一ブログカテゴリー取得
     *
     * @param BlogCategoriesServiceInterface $service
     * @param $blogCategoryId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogCategoriesServiceInterface $service, $blogCategoryId)
    {
        $this->request->allowMethod(['get']);
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogCategory = $message = null;
        try {
            $blogCategory = $service->get($blogCategoryId, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogCategory' => $blogCategory,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogCategory', 'message']);
    }

    /**
     * [API] ブログカテゴリーリスト取得
     *
     * @param BlogCategoriesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(BlogCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        if (!isset($queryParams['blog_content_id']) || empty($queryParams['blog_content_id'])) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogCategories = $message = null;
        try {
            $blogCategories = $service->getList($queryParams['blog_content_id'], $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogCategories' => $blogCategories,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogCategories', 'message']);
    }

}
