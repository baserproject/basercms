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

namespace BaserCore\Controller\Api;

use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class ContentFoldersController
 */
class ContentFoldersController extends BcApiController
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
        $this->set([
            'contentFolders' => $this->paginate($service->getIndex())
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
    public function view(ContentFoldersServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $this->set([
            'contentFolders' => $service->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
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
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $contentFolders = $service->create($this->request->getData());
            $message = __d('baser', 'コンテンツフォルダ「{0}」を追加しました。', $contentFolders->content->title);
            $this->set("contentFolder", $contentFolders);
            $this->set('content', $contentFolders->content);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $contentFolders = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->set(['errors' => $contentFolders->getErrors()]);
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set(['message' => $message]);
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
    public function delete(ContentFoldersServiceInterface $service, $id)
    {
        $this->request->allowMethod(['delete']);
        $contentFolders = $service->get($id);
        try {
            if ($service->delete($id)) {
                $message = __d('baser', 'コンテンツフォルダ: {0} を削除しました。', $contentFolders->content->title);
            }
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $contentFolders = $e->getEntity();
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'contentFolders' => $contentFolders
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders', 'message']);
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
        try {
            $contentFolder = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'フォルダー「{0}」を更新しました。', $contentFolder->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $contentFolder = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'contentFolder' => $contentFolder,
            'errors' => $contentFolder->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolder', 'message', 'errors']);
    }
}
