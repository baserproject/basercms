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
use BcBlog\Service\BlogCommentsService;
use BcBlog\Service\BlogCommentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogCommentsController
 */
class BlogCommentsController extends BcAdminApiController
{

    /**
     * [API] ブログコメント一覧取得
     *
     * @param BlogCommentsServiceInterface $service
     * @checked
     * @noTodo
     */
    public function index(BlogCommentsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        $queryParams = array_merge([
            'contain' => null,
        ], $queryParams);

        $this->set([
            'blogComments' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComments']);
    }

    /**
     * [API] 単一ブログコメントー取得
     *
     * @param BlogCommentsServiceInterface $service
     * @param $blogCommentId
     * @checked
     * @noTodo
     */
    public function view(BlogCommentsServiceInterface $service, $blogCommentId)
    {
        $this->request->allowMethod(['get']);

        $blogComment = $message = null;
        try {
            $blogComment = $service->get($blogCommentId, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogComment' => $blogComment,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComment', 'message']);
    }

    /**
     * [API] ブログコメント削除
     *
     * @param BlogCommentsServiceInterface $service
     * @param $blogCommentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogCommentsServiceInterface $service, $blogCommentId)
    {
        $this->request->allowMethod(['post', 'delete']);
        $blogComment = null;
        try {
            $blogComment = $service->get($blogCommentId);
            $service->delete($blogCommentId);
            $message = __d('baser_core', 'ブログコメント「{0}」を削除しました。', $blogComment->no);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'blogComment' => $blogComment
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComment', 'message']);
    }

    /**
     * ブログコメントのバッチ処理
     *
     * 指定したブログのコメントに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete','publish','unpublish'以外の値であれば500エラーを発生させる
     *
     * @param BlogCommentsService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(BlogCommentsServiceInterface $service)
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
        $errors = null;
        try {
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'ブログコメント「%s」を %s しました。'), implode(', ', $targets), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message, 'errors' => $errors]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * ブログコメントを登録する
     *
     * 画像認証を行い認証されればブログのコメントを登録する
     * コメント承認を利用していないブログの場合、公開されているコメント投稿者にアラートを送信する
     *
     * @param BlogCommentsServiceInterface $service
     * @throws Throwable
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function add(BlogCommentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $postData = $this->getRequest()->getData();

        if (!isset($queryParams['blog_content_id']) && !$postData['blog_content_id']) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_content_id が指定されていません。'));
        }

        if (!isset($queryParams['blog_post_id']) && !$postData['blog_post_id']) {
            throw new BcException(__d('baser_core', 'パラメーターに blog_post_id が指定されていません。'));
        }

        try {
            $entity = $service->add($postData['blog_content_id'], $postData['blog_post_id'], $postData);
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
        } catch (BcException $e) {
            $message = $e->getMessage();
            $this->setResponse($this->response->withStatus(400));
        } catch (Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $blogContent = $service->getBlogContent($postData['blog_content_id']);
        $service->sendCommentToAdmin($entity);
        if ($blogContent->comment_approve) {
            $service->sendCommentToContributor($entity);
        }

        $this->set([
            'blogComment' => $entity ?? null,
            'message' => $message ?? '',
            'errors' => $entity?->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'blogComment',
            'message',
            'errors'
        ]);
    }

}
