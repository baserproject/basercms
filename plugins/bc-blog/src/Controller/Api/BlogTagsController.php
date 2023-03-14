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
use BaserCore\Error\BcException;
use BcBlog\Service\BlogTagsService;
use BcBlog\Service\BlogTagsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * BlogTagsController
 */
class BlogTagsController extends BcApiController
{

    /**
     * [API] ブログタグ一覧取得
     *
     * @param BlogTagsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogTagsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'blogTags' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTags']);
    }

    /**
     * [API] 単一ブログタグー取得
     *
     * @param BlogTagsServiceInterface $service
     * @param $blogTagId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogTagsServiceInterface $service, $blogTagId)
    {
        $this->request->allowMethod(['get']);
        $blogTag = $message = null;
        try {
            $blogTag = $service->get($blogTagId);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogTag' => $blogTag,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTag', 'message']);
    }

    /**
     * [ADMIN] ブログタグ登録
     *
     * ブログのタグを登録する
     * ブログタグの登録に失敗した場合、HTTPレスポンスのステータスに400を返します。
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogTagsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogTag = $errors = null;
        try {
            /* @var \BcBlog\Service\BlogTagsService $service */
            $blogTag = $service->create($this->request->getData());
            $message = __d('baser_core', 'ブログタグ「{0}」を追加しました。', $blogTag->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogTag' => $blogTag,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'blogTag', 'errors']);
    }

    /**
     * [API] ブログタグ編集
     * @param BlogTagsServiceInterface $service
     * @param $blogTagId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogTagsServiceInterface $service, $blogTagId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $blogTag = $errors = null;
        try {
            $blogTag = $service->update($service->get($blogTagId), $this->request->getData());
            $message = __d('baser_core', 'ブログタグ「{0}」を更新しました。', $blogTag->name);
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
            'blogTag' => $blogTag,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTag', 'message', 'errors']);
    }

    /**
     * [API] ブログタグ削除
     * @param BlogTagsServiceInterface $service
     * @param $blogTagId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogTagsServiceInterface $service, $blogTagId)
    {
        $this->request->allowMethod(['post', 'delete']);
        $blogTag = null;
        try {
            $blogTag = $service->get($blogTagId);
            $service->delete($blogTagId);
            $message = __d('baser_core', 'ブログタグ「{0}」を削除しました。', $blogTag->name);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogTag' => $blogTag
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTag', 'message']);
    }

    /**
     * ブログタグのバッチ処理
     *
     * 指定したブログのコメントに対して削除処理を一括で行う
     *
     * ### エラー
     * delete以外のHTTPメソッドには500エラーを返す
     * 一括処理に失敗すると400エラーを返す
     *
     * @param BlogTagsService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(BlogTagsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除'),
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
                sprintf(__d('baser_core', 'ブログタグ「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
