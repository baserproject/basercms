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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
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
    public function view(ContentsServiceInterface $service, int $id)
    {
        $content = $message = null;
        try {
            $content = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'content' => $content,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
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
        $trash = $message = null;
        try {
            $trash = $service->getTrash($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'content' => $trash,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
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
    public function index(ContentsServiceInterface $service, string $type = "index")
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
        $content = $message = $errors = null;
        try {
            $id = $this->request->getData('id');
            $content = $service->get($id);
            $children = $service->getChildren($id);
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
                    foreach ($children as $child) {
                        $text .= "\nコンテンツ: " . $child->title . "を削除しました。";
                    }
                }
                $message = __d('baser', $text);
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'content' => $content,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message', 'errors']);
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
        try {
            $trash = $service->getTrashIndex($this->request->getQueryParams())->order(['plugin', 'type']);

            // EVENT Contents.beforeTrashEmpty
            $this->dispatchLayerEvent('beforeTrashEmpty', [
                'data' => $trash
            ]);

            $result = true;
            foreach ($trash as $entity) {
                if (!$service->hardDeleteWithAssoc($entity->id)) $result = false;
            }
            $message = __d('baser', 'ゴミ箱を空にしました。');

            // EVENT Contents.afterTrashEmpty
            $this->dispatchLayerEvent('afterTrashEmpty', [
                'data' => $result
            ]);

        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * コンテンツ情報編集
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $content = $errors = null;
        try {
            $content = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'コンテンツ「{0}」を更新しました。', $content->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'content' => $content,
            'message' => $message,
            'errors' => $errors,
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
    public function trash_return(ContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get', 'head']);
        $restored = null;
        try {
            if ($restored = $service->restore($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を元に戻しました。', $restored->title);
            } else {
                $message = __d('baser', 'ゴミ箱の復元に失敗しました');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
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
        $content = $errors = $message = null;
        $result = false;
        if ($id && $status) {
            try {
                switch ($status) {
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
            } catch (PersistenceFailedException $e) {
                $errors = $e->getEntity()->getErrors();
                $message = __d('baser', "入力エラーです。内容を修正してください。");
                $this->setResponse($this->response->withStatus(400));
            } catch (\Throwable $e) {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
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
            'content' => $content,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message', 'errors']);
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
    public function get_full_url(ContentsServiceInterface $service, int $id)
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
    public function exists(ContentsServiceInterface $service, int $id)
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
    public function get_content_folder_list(ContentsServiceInterface $service, int $siteId)
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
        $newContent = $url = $errors = null;
        try {
            $oldContent = $service->get($this->request->getData('id'));
            $newContent = $service->rename($oldContent, $this->getRequest()->getData());
            $url = $service->getUrlById($newContent->id);
            $this->setResponse($this->response->withStatus(200));
            $message = sprintf(
                __d('baser', '「%s」を「%s」に名称変更しました。'),
                $oldContent->title,
                $newContent->title
            );
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'name' => $newContent?->name,
            'url' => $url,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['name', 'url', 'message', 'errors']);
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
        $alias = $errors = null;
        try {
            $alias = $service->alias($this->request->getData('content'));
            $message = __d('baser', '{0} を作成しました。', $alias->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'content' => $alias,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message', 'errors']);
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
        $url = $content = $errors = null;
        if (!$service->isTreeModifiedByAnotherUser($this->getRequest()->getData('listDisplayed'))) {
            try {
                // EVENT Contents.beforeMove
                $event = $this->dispatchLayerEvent('beforeMove', [
                    'data' => $this->getRequest()->getData()
                ]);
                if ($event !== false) {
                    $this->getRequest()->withParsedBody($event->getResult() === true ? $event->getData('data') : $event->getResult());
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

            } catch (PersistenceFailedException $e) {
                $errors = $e->getEntity()->getErrors();
                $message = __d('baser', "入力エラーです。内容を修正してください。");
                $this->setResponse($this->response->withStatus(400));
            } catch (\Throwable $e) {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
        } else {
            $message = __d('baser', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。');
        }

        $this->set([
            'url' => $url,
            'content' => $content,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['url', 'content', 'message', 'errors']);
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
        $errors = null;
        try {
            $targets = $this->getRequest()->getData('batch_targets');
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'コンテンツ 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set(['message' => $message, 'errors' => $errors]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }
}
