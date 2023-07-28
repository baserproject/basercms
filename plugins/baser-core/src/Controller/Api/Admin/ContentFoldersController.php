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

namespace BaserCore\Controller\Api\Admin;

use BaserCore\Service\ContentFoldersServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentFoldersController
 */
class ContentFoldersController extends BcAdminApiController
{

    /**
     * コンテンツフォルダ一覧取得
     * @param ContentFoldersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentFoldersServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = array_merge([
            'contain' => null,
        ], $this->getRequest()->getQueryParams());

        $this->set([
            'contentFolders' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * コンテンツフォルダ取得
     * @param ContentFoldersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentFoldersServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');

        $contentFolder = $message = null;
        try {
            $contentFolder = $service->get($id, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'contentFolder' => $contentFolder,
            'content' => $contentFolder?->content,
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolder', 'content', 'message']);
    }

    /**
     * コンテンツフォルダ登録
     * @param ContentFoldersServiceInterface $service
     * @checked
     * @unitTest
     * @noTodo
     */
    public function add(ContentFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $contentFolder = $errors = null;
        try {
            $contentFolder = $service->create($this->request->getData());
            $message = __d('baser_core', 'コンテンツフォルダ「{0}」を追加しました。', $contentFolder->content->title);
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
            'contentFolder' => $contentFolder,
            'content' => $contentFolder?->content,
            'message' => $message,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', [
            'contentFolder',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * コンテンツフォルダ削除
     * @param ContentFoldersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentFoldersServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['delete']);
        $contentFolder = null;
        try {
            $contentFolder = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'コンテンツフォルダ: {0} を削除しました。', $contentFolder->content->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'contentFolder' => $contentFolder,
            'content' => $contentFolder ?? $contentFolder->content,
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolder', 'content', 'message']);
    }

    /**
     * コンテンツフォルダー情報編集
     * @param ContentFoldersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentFoldersServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $contentFolder = $errors = null;
        try {
            $contentFolder = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'フォルダー「{0}」を更新しました。', $contentFolder->content->title);
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
            'contentFolder' => $contentFolder,
            'content' => $contentFolder ?? $contentFolder->content,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolder', 'content', 'message', 'errors']);
    }
}
