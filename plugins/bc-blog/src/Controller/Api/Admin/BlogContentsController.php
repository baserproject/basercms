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
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogContentsController
 */
class BlogContentsController extends BcAdminApiController
{

    /**
     * [API] ブログコンテンツー一覧取得
     *
     * @param BlogContentsServiceInterface $blogContentsService
     * @checked
     * @noTodo
     */
    public function index(BlogContentsServiceInterface $blogContentsService)
    {
        $this->request->allowMethod(['get']);

        $this->set([
            'blogContents' => $this->paginate($blogContentsService->getIndex($this->getRequest()->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContents']);
    }

    /**
     * [API] 単一ブログコンテンツー取得
     *
     * @param BlogContentsServiceInterface $service
     * @param $blogContentId
     * @checked
     * @noTodo
     */
    public function view(BlogContentsServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['get']);

        $blogContent = $message = null;
        try {
            $blogContent = $service->get($blogContentId, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogContent' => $blogContent,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'message']);
    }

    /**
     * [API] ブログコンテンツリスト取得
     *
     * @param BlogContentsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(BlogContentsServiceInterface $service)
    {
        $this->set([
            'blogContents' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContents']);
    }

    /**
     * [API] ブログコンテンツー新規追加
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogContent = $errors = null;
        try {
            $blogContent = $service->create($this->request->getData());
            $message = __d('baser_core', 'ブログ「{0}」を追加しました。', $blogContent->content->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'blogContent' => $blogContent,
            'content' => $blogContent?->content,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'blogContent', 'content', 'errors']);
    }

    /**
     * [API] ブログコンテンツー編集
     * @param BlogContentsServiceInterface $service
     * @param $blogContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogContentsServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogContent = $errors = null;
        try {
            $blogContent = $service->update($service->get($blogContentId), $this->request->getData());
            $message = __d('baser_core', 'ブログ「{0}」を更新しました。', $blogContent->content->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'message', 'errors']);
    }

    /**
     * [API] ブログコンテンツ削除
     * @param BlogContentsServiceInterface $service
     * @param $blogContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogContentsServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['post', 'delete']);
        $blogContent = null;
        try {
            $blogContent = $service->get($blogContentId);
            if ($service->delete($blogContentId)) {
                $message = __d('baser_core', 'ブログコンテンツ「{0}」を削除しました。', $blogContent->description);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', '入力エラーです。内容を修正してください。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'message']);
    }

    /**
     * コピー
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(BlogContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogContent = $content = null;
        try {
            $blogContent = $service->copy($this->request->getData());
            if (!$blogContent) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'コピーに失敗しました。データが不整合となっている可能性があります。');
            } else {
                $content = $blogContent->content;
                $message = __d('baser_core', 'ブログのコピー「{0}」を追加しました。', $blogContent->content->title);
                $this->BcMessage->setSuccess($message, true, false);
            }

        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent,
            'content' => $content
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'content', 'message']);
    }

}
