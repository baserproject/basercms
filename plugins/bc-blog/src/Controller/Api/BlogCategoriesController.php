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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * BlogCategoriesController
 */
class BlogCategoriesController extends BcApiController
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
     * @param $blogContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogCategoriesServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['get']);
        $blogCategories = $message = null;
        try {
            $blogCategories = $this->paginate($service->getIndex($blogContentId, $this->request->getQueryParams()));
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
            $blogCategory = $service->get($blogCategoryId);
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
     * @param $blogContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(BlogCategoriesServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['get']);
        $blogCategories = $message = null;
        try {
            $blogCategories = $service->getList($blogContentId);
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
     * @param $blogContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogCategoriesServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['post']);
        $blogCategory = $errors = null;
        try {
            $blogCategory = $service->create($blogContentId, $this->request->getData());
            $message = __d('baser_core', 'ブログカテゴリー「{0}」を追加しました。', $blogCategory->name);
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
