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
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogPostsController
 */
class BlogPostsController extends BcAdminApiController
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

        $queryParams = array_merge([
            'contain' => null,
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

        $blogPost = $message = null;
        try {
            $blogPost = $service->get($id, $this->getRequest()->getQueryParams());
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

    /**
     * [API] ブログ記事新規追加
     *
     * @param BlogPostsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogPostsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $blogPost = $errors = null;
        try {
            $blogPost = $service->create($this->request->getData());
            $message = __d('baser_core', '記事「{0}」を追加しました。', $blogPost->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'blogPost' => $blogPost,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'blogPost', 'errors']);
    }

    /**
     * [API] ブログ記事編集のAPI実装
     *
     * 指定したブログ記事を編集する。
     * 記事の保存に失敗した場合、PersistenceFailedExceptionかBcExceptionのエラーが発生する。
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogPost = $errors = null;
        try {
            $blogPost = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', '記事「{0}」を更新しました。', $blogPost->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'blogPost' => $blogPost,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', ['blogPost', 'message', 'errors']);
    }

    /**
     * [API] ブログ記事複製のAPI実装
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $blogPostCopied = null;
        try {
            $blogPost = $service->get($id);
            $blogPostCopied = $service->copy($id);
            $message = __d('baser_core', 'ブログ記事「{0}」をコピーしました。', $blogPost->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'blogPost' => $blogPostCopied,
            'message' => $message
        ]);

        $this->viewBuilder()->setOption('serialize', ['blogPost', 'message']);
    }

    /**
     * [API] ブログ記事を公開状態に設定のAPI実装
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        try {
            $result = $service->publish($id);
            if ($result) {
                $message = __d('baser_core', 'ブログ記事「{0}」を公開状態にしました。', $result->title);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ブログ記事を非公開状態に設定のAPI実装
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        try {
            $result = $service->unpublish($id);
            if ($result) {
                $message = __d('baser_core', 'ブログ記事「{0}」を非公開状態にしました。', $result->title);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ブログ記事削除処理
     *
     * 指定したブログ記事を削除する
     *
     * @param BlogPostsServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogPostsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'delete']);

        $blogPost = null;
        try {
            $blogPost = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'ブログ記事「{0}」を削除しました。', $blogPost->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'blogPost' => $blogPost,
            'message' => $message
        ]);

        $this->viewBuilder()->setOption('serialize', ['blogPost', 'message']);
    }

    /**
     * ブログ記事のバッチ処理
     *
     * 指定したブログ記事に対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete','publish','unpublish'以外の値であれば500エラーを発生させる
     *
     * @param BlogPostsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(BlogPostsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除'),
            'publish' => __d('baser_core', '公開'),
            'unpublish' => __d('baser_core', '非公開に')
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'ブログ記事「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
