<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Service\ContentFolderServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;


/**
 * Class ContentFoldersController
 *
 * https://localhost/baser/api/baser-core/user_groups/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class ContentFoldersController extends BcApiController
{
    /**
     * ユーザーグループ一覧取得
     * @param ContentFolderServiceInterface $ContentFolders
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentFolderServiceInterface $ContentFolders)
    {
        $this->request->allowMethod('get');
        $this->set([
            'contentFolders' => $this->paginate($ContentFolders->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * ユーザーグループ取得
     * @param ContentFolderServiceInterface $ContentFolders
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentFolderServiceInterface $ContentFolders, $id)
    {
        $this->request->allowMethod('get');
        $this->set([
            'contentFolders' => $ContentFolders->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * ユーザーグループ登録
     * @param ContentFolderServiceInterface $ContentFolders
     * @checked
     * @unitTest
     */
    public function add(ContentFolderServiceInterface $ContentFolders)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $contentFolders = $ContentFolders->create($this->request->getData());
            if (!$contentFolders->getErrors()) {
                $message = __d('baser', 'コンテンツフォルダ「{0}」を追加しました。', $contentFolders->content->name);
                // TODO: contentFOlderを追加するかどうか
                $this->set('content', $contentFolders->content);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', "入力エラーです。内容を修正してください。\n" . $contentFolders->getErrors());
            }
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', "無効な処理です。\n" . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content']);
    }

    /**
     * ユーザーグループ削除
     * @param ContentFolderServiceInterface $ContentFolders
     * @param $id
     */
    public function delete(ContentFolderServiceInterface $ContentFolders, $id)
    {
        $this->request->allowMethod(['delete']);
        $contentFolders = $ContentFolders->get($id);
        try {
            if ($ContentFolders->delete($id)) {
                $message = __d('baser', 'コンテンツフォルダ: {0} を削除しました。', $contentFolders->name);
            }
        } catch (Exception $e) {
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
     * @param ContentFolderServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentFolderServiceInterface $contentFolders, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $contentFolder = $contentFolders->update($contentFolders->get($id), $this->request->getData());
        if (!$contentFolder->getErrors()) {
            $message = __d('baser', 'フォルダー「{0}」を更新しました。', $contentFolder->name);
        } else {
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
