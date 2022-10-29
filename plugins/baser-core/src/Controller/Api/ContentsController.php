<?php
/**
 * baserCMS :  Based Webcontent Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Error\BcException;
use Exception;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Service\ContentsServiceInterface;

/**
 * Class ContentsController
 */
class ContentsController extends BcApiController
{

    /**
     * コンテンツ情報取得
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentsServiceInterface $service, $id)
    {
        $this->set([
            'content' => $service->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['content']);
    }

    /**
     * ゴミ箱情報取得
     * @param ContentsServiceInterface $Contents
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view_trash(ContentsServiceInterface $service, $id)
    {
        $this->set([
            'trash' => $service->getTrash($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash']);
    }

    /**
     * コンテンツ情報一覧取得
     *
     * @param ContentsServiceInterface $service
     * @param string $type
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentsServiceInterface $service, $type = "index")
    {
        switch($type) {
            case "index":
                $data = $this->paginate($service->getIndex($this->request->getQueryParams()));
                break;
            case "trash":
                $data = $this->paginate($service->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']));
                break;
            case "tree":
                $data = $this->paginate($service->getTreeIndex($this->request->getQueryParams()));
                break;
            case "table":
                $data = $this->paginate($service->getTableIndex($this->request->getQueryParams()));
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
     * @param ContentsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('id');
        $content = $service->get($id);
        $children = $service->getChildren($id);
        try {

            // EVENT Contents.beforeDelete
            $this->dispatchLayerEvent('beforeDelete', [
                'data' => $id
            ]);

            if ($service->deleteRecursive($id)) {

                // EVENT Contents.afterDelete
                $this->dispatchLayerEvent('afterDelete', [
                    'data' => $id
                ]);

                $text = "コンテンツ: " . $content->title . "を削除しました。";
                if ($children) {
                    $content = array_merge([$content], $children->toArray());
                    foreach($children as $child) {
                        $text .= "\nコンテンツ: " . $child->title . "を削除しました。";
                    }
                }
                $message = __d('baser', $text);
                $this->set(['content' => $content]);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['contents', 'message']);
    }

    /**
     * ゴミ箱を空にする(物理削除)
     * @param ContentsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_empty(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trash = $service->getTrashIndex($this->request->getQueryParams())->order(['plugin', 'type']);

        // EVENT Contents.beforeTrashEmpty
        $this->dispatchLayerEvent('beforeTrashEmpty', [
            'data' => $trash
        ]);

        try {
            $result = true;
            foreach($trash as $entity) {
                if (!$service->hardDeleteWithAssoc($entity->id)) $result = false;
            }
            $message = __d('baser', 'ゴミ箱を空にしました。');

            // EVENT Contents.afterTrashEmpty
            $this->dispatchLayerEvent('afterTrashEmpty', [
                'data' => $result
            ]);

        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
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
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $content = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'コンテンツ「{0}」を更新しました。', $content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
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
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['get', 'head']);
        try {
            if ($restored = $service->restore($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を元に戻しました。', $restored->title);
            } else {
                $message = __d('baser', 'ゴミ箱の復元に失敗しました');
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'content' => $restored
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }

    /**
     * 公開状態を変更する
     * @param ContentsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function change_status(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $id = $this->request->getData('id');
        $status = $this->request->getData('status');

        // EVENT Contents.beforeChangeStatus
        $this->dispatchLayerEvent('beforeChangeStatus', [
            'id' => $id,
            'status' => $status
        ]);

        $result = false;
        if ($id && $status) {
            try {
                switch($status) {
                    case 'publish':
                        $content = $service->publish($id);
                        $message = __d('baser', 'コンテンツ: {0} を公開しました。', $content->title);
                        break;
                    case 'unpublish':
                        $content = $service->unpublish($id);
                        $message = __d('baser', 'コンテンツ: {0} を非公開にしました。', $content->title);
                        break;
                }
                $result = true;
            } catch (\Exception $e) {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '無効な処理です。') . "データが不足しています";
        }

        // EVENT Contents.afterChangeStatus
        $this->dispatchLayerEvent('afterChangeStatus', [
            'id' => $id,
            'result' => $result
        ]);

        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }

    /**
     * get_full_url
     *
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @unitTest
     * @noTodo
     */
    public function get_full_url(ContentsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['get']);
        if ($id) {
            $this->set(['fullUrl' => $service->getUrlById($id, true)]);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $this->set(['message' => __d('baser', '無効な処理です。')]);
        }
        $this->viewBuilder()->setOption('serialize', ['message', 'fullUrl']);
    }

    /**
     * 指定したIDのコンテンツが存在するか確認する
     * ゴミ箱のものは無視
     *
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function exists(ContentsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['get']);
        $this->set(['exists' => $service->exists($id)]);
        $this->viewBuilder()->setOption('serialize', ['exists']);
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     * @param ContentsServiceInterface $service
     * @param int $siteId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_content_folder_list(ContentsServiceInterface $service, $siteId)
    {
        $this->request->allowMethod(['get']);
        $this->set(['list' => $service->getContentFolderList($siteId, ['conditions' => ['site_root' => false]])]);
        $this->viewBuilder()->setOption('serialize', ['list']);
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     * @param ContentsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rename(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $oldContent = $service->get($this->request->getData('id'));
            $newContent = $service->rename($oldContent, $this->getRequest()->getData());
            $this->setResponse($this->response->withStatus(200));
            $message = sprintf(
                __d('baser', '「%s」を「%s」に名称変更しました。'),
                $oldContent->title,
                $newContent->title
            );
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $content = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = $content->getErrors();
        }
        $this->set([
            'message' => $message,
            'name' => $newContent->name,
            'url' => $service->getUrlById($newContent->id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'name', 'url']);
    }

    /**
     * add_alias
     *
     * @param ContentsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add_alias(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        try {
            $alias = $service->alias($this->request->getData('content'));
            $message = __d('baser', '{0} を作成しました。', $alias->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $alias = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', "無効な処理です。\n" . $alias->getErrors());
        }
        $this->set(['content' => $alias, 'message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content']);
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     * @param ContentsServiceInterface $service
     * @return \Cake\Http\Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function is_unique_content(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        if (!$this->request->getData('url')) {
            $this->setResponse($this->response->withStatus(500));
        } else {
            return $this->response->withType("application/json")->withStringBody(
                json_encode(!$service->existsContentByUrl($this->request->getData('url')))
            );
        }
    }

    /**
     * 並び順を移動する
     * @param ContentsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $url = $content = null;
        if (!$service->isTreeModifiedByAnotherUser($this->getRequest()->getData('listDisplayed'))) {
            try {
                // EVENT Contents.beforeMove
                $event = $this->dispatchLayerEvent('beforeMove', [
                    'data' => $this->getRequest()->getData()
                ]);
                if ($event !== false) {
                    $this->getRequest()->withParsedBody($event->getResult() === true? $event->getData('data') : $event->getResult());
                }

                $beforeContent = $service->get($this->request->getData('origin.id'));
                $beforeUrl = $beforeContent->url;
                $content = $service->move($this->request->getData('origin'), $this->request->getData('target'));
                $message = sprintf(
                    __d('baser', "コンテンツ「%s」の配置を移動しました。\n%s > %s"),
                    $content->title,
                    rawurldecode($beforeUrl),
                    rawurldecode($content->url)
                );
                $url = $service->getUrlById($content->id, true);

                // EVENT Contents.afterMove
                $this->dispatchLayerEvent('afterMove', [
                    'data' => $content
                ]);

            } catch (Exception $e) {
                $message = __d('baser', 'データ保存中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
        } else {
            $message = __d('baser', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。');
        }

        $this->set([
            'message' => $message,
            'url' => $url,
            'content' => $content
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'url', 'content']);
    }

    /**
     * batch
     *
     * @param ContentsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'publish' => '公開',
            'unpublish' => '非公開に',
            'delete' => '削除',
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
                sprintf(__d('baser', 'コンテンツ 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }
}
