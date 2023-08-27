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
namespace BcBlog\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BaserCore\Error\BcException;
use BcBlog\Service\BlogCategoriesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogCategoriesController
 */
class BlogCategoriesController extends BcAdminApiController
{

    /**
     * バッチ処理
     *
     * @param BlogCategoriesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(BlogCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        try {
            $targets = $this->getRequest()->getData('batch_targets');
            $service->batch($method, $targets);
            $names = $service->getNamesById($targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'ブログカテゴリ NO.%s を %s しました。'), implode(', ', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

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

        if (!isset($queryParams['blog_content_id']) || empty($queryParams['blog_content_id'])) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

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

        $blogCategory = $message = null;
        try {
            $blogCategory = $service->get($blogCategoryId, $this->getRequest()->getQueryParams());
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

        if (!isset($queryParams['blog_content_id']) || empty($queryParams['blog_content_id'])) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

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

    /**
     * [API] ブログカテゴリー新規追加
     * @param BlogCategoriesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);

        $postData = $this->getRequest()->getData();

        if (!isset($postData['blog_content_id']) || empty($postData['blog_content_id'])) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

        $blogCategory = $errors = null;
        try {
            $blogCategory = $service->create($postData['blog_content_id'], $postData);
            $message = __d('baser_core', 'ブログカテゴリー「{0}」を追加しました。', $blogCategory->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'blogCategory' => $blogCategory,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'blogCategory', 'errors']);
    }

    /**
     * [API] ブログカテゴリー編集
     *
     * @param BlogCategoriesServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogCategoriesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogCategory = $errors = null;
        try {
            $blogCategory = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'ブログカテゴリー「{0}」を更新しました。', $blogCategory->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'blogCategory' => $blogCategory,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogCategory', 'message', 'errors']);
    }

    /**
     * [API] ブログカテゴリー削除
     * @param BlogCategoriesServiceInterface $service
     * @param $blogCategoryId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogCategoriesServiceInterface $service, $blogCategoryId)
    {
        $this->request->allowMethod(['post', 'delete']);
        $blogCategory = null;
        try {
            $blogCategory = $service->get($blogCategoryId);
            if ($service->delete($blogCategoryId)) {
                $message = __d('baser_core', 'ブログカテゴリー「{0}」を削除しました。', $blogCategory->name);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'blogCategory' => $blogCategory
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogCategory', 'message']);
    }
}
