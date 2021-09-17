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
        $this->set([
            'contentFolders' => $ContentFolders->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * ユーザーグループ登録
     * @param ContentFolderServiceInterface $ContentFolders
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(ContentFolderServiceInterface $ContentFolders)
    {
        if ($this->request->is('post')) {
            $contentFolders = $ContentFolders->create($this->request->getData());
            if (!$contentFolders->getErrors()) {
                $message = __d('baser', 'コンテンツフォルダ「{0}」を追加しました。', $contentFolders->name);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'contentFolders' => $contentFolders,
            'errors' => $contentFolders->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'contentFolders']);
    }

    /**
     * ユーザーグループ削除
     * @param ContentFolderServiceInterface $ContentFolders
     * @param $id
     */
    public function delete(ContentFolderServiceInterface $ContentFolders, $id)
    {
        $contentFolders = $ContentFolders->get($id);
        if ($this->request->is(['post', 'delete'])) {
            try {
                if ($ContentFolders->delete($id)) {
                    $message = __d('baser', 'コンテンツフォルダ: {0} を削除しました。', $contentFolders->name);
                }
            } catch (Exception $e) {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        }
        $this->set([
            'message' => $message,
            'contentFolders' => $contentFolders
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders', 'message']);
    }
}
