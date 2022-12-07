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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcUtil;
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * BlogContentsController
 */
class BlogContentsController extends BcApiController
{

    /**
     * [API] ブログコンテンツー一覧取得
     *
     * @param BlogContentsServiceInterface $blogContentsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogContentsServiceInterface $blogContentsService)
    {
        $this->set([
            'blogContents' => $this->paginate($blogContentsService->getIndex($this->request->getQueryParams()))
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
     * @unitTest
     */
    public function view(BlogContentsServiceInterface $service, $blogContentId)
    {
        $this->set([
            'blogContent' => $service->get($blogContentId)
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent']);
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
        try {
            $blogContent = $service->create($this->request->getData());
            $message = __d('baser', 'ブログ「{0}」を追加しました。', $blogContent->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $blogContent = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'blogContent' => $blogContent,
            'content' => $blogContent->content,
            'message' => $message,
            'errors' => $blogContent->getErrors()
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

        try {
            $blogContent = $service->update($service->get($blogContentId), $this->request->getData());
            $message = __d('baser', 'ブログ「{0}」を更新しました。', $blogContent->content->title);
        } catch (PersistenceFailedException $e) {
            $blogContent = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent,
            'errors' => $blogContent->getErrors()
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
        if ($this->request->is(['post', 'delete'])) {
            try {
                $blogContent = $service->get($blogContentId);
                if ($service->delete($blogContentId)) {
                    $message = __d('baser', 'ブログコンテンツ「{0}」を削除しました。', $blogContent->description);
                } else {
                    $this->setResponse($this->response->withStatus(400));
                    $message = __d('baser', '入力エラーです。内容を修正してください。');
                }
            } catch (PersistenceFailedException $e) {
                $this->setResponse($this->response->withStatus(400));
                $blogContent = $e->getEntity();
                $message = __d('baser', 'データベース処理中にエラーが発生しました。');
            }
            $this->set([
                'message' => $message,
                'blogContent' => $blogContent,
                'errors' => $blogContent->getErrors()
            ]);
            $this->viewBuilder()->setOption('serialize', ['blogContent', 'message', 'errors']);
        }
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
        $errors = null;
        try {
            $blogContent = $service->copy($this->request->getData());
            if (!$blogContent) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
            } else {
                $message = __d('baser', 'ブログのコピー「{0}」を追加しました。', $blogContent->content->title);
            }

        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(500));
            $errors = $e->getEntity();
            $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent,
            'content' => $blogContent? $blogContent->content : null,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'content', 'message', 'errors']);
    }

}
