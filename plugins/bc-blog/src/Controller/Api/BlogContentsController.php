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
     */
    public function index()
    {
        //todo ブログコンテンツー一覧取得
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
     * [API] 単一ブログコンテンツー取得
     */
    public function list()
    {
        //todo 単一ブログコンテンツー取得
    }

    /**
     * [API] ブログコンテンツー新規追加
     * @checked
     * @noTodo
     */
    public function add(BlogContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $blogContent = $service->create($this->request->getData());
            $message = __d('baser', 'ブログ「{0}」を追加しました。', $blogContent->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $blogContent = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'blogContent' => $blogContent,
            'message' => $message,
            'errors' => $blogContent->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content', 'errors']);
    }

    /**
     * [API] ブログコンテンツー編集
     */
    public function edit()
    {
        //todo ブログコンテンツー編集
    }

    /**
     * [API] ブログコンテンツー削除
     */
    public function delete()
    {
        //todo ブログコンテンツー削除
    }

    /**
     * コピー
     *
     * @checked
     * @noTodo
     */
    public function copy(BlogContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $blogContent = $service->copy($this->request->getData());
            $message = __d('baser', 'ブログのコピー「%s」を追加しました。', $blogContent->content->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(500));
            $blogContent = $e->getEntity();
            $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
        }
        $this->set([
            'message' => $message,
            'blogContent' => $blogContent,
            'content' => $blogContent->content,
            'errors' => $blogContent->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'content', 'message', 'errors']);
    }

}
