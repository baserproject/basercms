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
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class ContentFoldersController
 *
 * https://localhost/baser/api/baser-core/user_groups/action_name.json で呼び出す
 *
 */
class ContentFoldersController extends BcApiController
{
    /**
     * コンテンツフォルダ一覧取得
     * @param ContentFoldersServiceInterface $ContentFolders
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentFoldersServiceInterface $ContentFolders)
    {
        $this->request->allowMethod('get');
        $this->set([
            'contentFolders' => $this->paginate($ContentFolders->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * コンテンツフォルダ取得
     * @param ContentFoldersServiceInterface $ContentFolders
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentFoldersServiceInterface $ContentFolders, $id)
    {
        $this->request->allowMethod('get');
        $this->set([
            'contentFolders' => $ContentFolders->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * コンテンツフォルダ登録
     * @param ContentFoldersServiceInterface $ContentFolders
     * @checked
     * @unitTest
     * @noTodo
     */
    public function add(ContentFoldersServiceInterface $ContentFolders)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $contentFolders = $ContentFolders->create($this->request->getData());
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
        $this->viewBuilder()->setOption('serialize', ['message', 'content', 'errors']);
    }

    /**
     * コンテンツフォルダ削除
     * @param ContentFoldersServiceInterface $ContentFolders
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentFoldersServiceInterface $ContentFolders, $id)
    {
        $this->request->allowMethod(['delete']);
        $contentFolders = $ContentFolders->get($id);
        try {
            if ($ContentFolders->delete($id)) {
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
     * @param ContentFoldersServiceInterface $contentFolders
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentFoldersServiceInterface $contentFolders, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $contentFolder = $contentFolders->update($contentFolders->get($id), $this->request->getData());
            $message = __d('baser', 'フォルダー「{0}」を更新しました。', $contentFolder->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $contentFolders = $e->getEntity();
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
