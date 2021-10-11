<?php
/**
 * baserCMS :  Based Webcontent Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Service\ContentServiceInterface;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentsController
 * @package BaserCore\Controller\Api
 */
class ContentsController extends BcApiController
{

    /**
     * コンテンツ情報取得
     * @param ContentServiceInterface $Contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentServiceInterface $contentService, $id)
    {
        $this->set([
            'content' => $contentService->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['content']);
    }
    /**
     * ゴミ箱情報取得
     * @param ContentServiceInterface $Contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view_trash(ContentServiceInterface $contentService, $id)
    {
        $this->set([
            'trash' => $contentService->getTrash($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash']);
    }

    /**
     * コンテンツ情報一覧取得
     *
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentServiceInterface $contentService, $type="index")
    {
        switch ($type) {
            case "index":
                $data = $this->paginate($contentService->getIndex($this->request->getQueryParams()));
                break;
            case "trash":
                $data = $this->paginate($contentService->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']));
                break;
            case "tree":
                $data = $this->paginate($contentService->getTreeIndex($this->request->getQueryParams()));
                break;
            case "table":
                $data = $this->paginate($contentService->getTableIndex($this->request->getQueryParams()));
                break;
        }
        $this->set([
            'contents' => $data
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }

    /**
     * コンテンツ情報削除(論理削除)
     * ※ 子要素があれば、子要素も削除する
     * @param ContentServiceInterface $contentService
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contents = $contentService->get($id);
        $children = $contentService->getChildren($id);
        try {
            $text = "コンテンツ: " . $contents->name . "を削除しました。";
            if ($children && $contentService->treeDelete($id)) {
                $contents = array_merge([$contents], $children->toArray());
                foreach ($children as $child) {
                    $text .= "\nコンテンツ: " . $child->name . "を削除しました。";
                }
                $message = __d('baser', $text);
            } elseif ($contentService->delete($id)) {
                $message = __d('baser', $text);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'contents' => $contents
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents', 'message']);
    }

    /**
     * ゴミ箱内コンテンツ情報を削除する(物理削除)
     * @param ContentServiceInterface $contentService
     * @param $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_trash(ContentServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trash = $contentService->getTrash($id);
        try {
            if ($contentService->hardDeleteWithAssoc($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を削除しました。', $trash->name);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'trash' => $trash
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash', 'message']);
    }

    /**
     * ゴミ箱を空にする(物理削除)
     * @param ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_empty(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trash = $contentService->getTrashIndex($this->request->getQueryParams());
        $text = "ゴミ箱: ";
        try {
            foreach ($trash as $entity) {
                if ($contentService->hardDeleteWithAssoc($entity->id)) {
                    $text .=  "$entity->name($entity->type)" . "を削除しました。";
                    }
            }
            $message = __d('baser', $text);
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'trash' => $trash
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash', 'message']);
    }

    /**
     * コンテンツ情報編集
     * @param ContentServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentServiceInterface $contents, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $content = $contents->update($contents->get($id), $this->request->getData());
        if (!$content->getErrors()) {
            $message = __d('baser', 'コンテンツ「{0}」を更新しました。', $content->name);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'content' => $content,
            'errors' => $content->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message', 'errors']);
    }

    /**
     * trash_return
     *
     * コンテンツ情報を元に戻す
     * @param ContentServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentServiceInterface $contents, $id)
    {
        $this->request->allowMethod(['get', 'head']);
        try {
            if ($restored = $contents->restore($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を元に戻しました。', $restored->name);
            } else {
                $message = __d('baser', 'ゴミ箱の復元に失敗しました');
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'content' => $restored
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }
}
