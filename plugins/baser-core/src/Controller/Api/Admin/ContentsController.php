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

namespace BaserCore\Controller\Api\Admin;

use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentsController
 */
class ContentsController extends BcAdminApiController
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
            $content = $service->get($id, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
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
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
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
    public function index(ContentsServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $params = array_merge([
            'list_type' => 'index',
            'contain' => null,
        ], $this->getRequest()->getQueryParams());
        $type = $params['list_type'];
        unset($params['list_type']);

        switch ($type) {
            case "index":
                $entities = $this->paginate($service->getTableIndex($params));
                break;
            case "tree":
                $entities = $this->paginate($service->getTreeIndex($params));
                break;
        }

        $this->set(['contents' => $entities]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }

    /**
     * ゴミ箱内のコンテンツ一覧を取得する
     *
     * @param ContentsService $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index_trash(ContentsServiceInterface $service)
    {
        $entities = $this->paginate($service->getTrashIndex(
            $this->request->getQueryParams(), 'threaded'
        )->orderBy(['site_id', 'lft']));

        $this->set(['contents' => $entities]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }

    /**
     * コンテンツ情報削除(論理削除)
     * ※ 子要素があれば、子要素も削除する
     * @param ContentsServiceInterface|ContentsService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentsServiceInterface $service, $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $content = $message = $errors = null;
        try {
            if(!$id) $id = $this->request->getData('id');

            $content = $service->get($id);
            $children = $service->getChildren($id);
            if ($service->deleteRecursive($id)) {
                $text = __d('baser_core', "コンテンツ: {0}を削除しました。", $content->title);
                if ($children) {
                    $content = array_merge([$content], $children->toArray());
                    foreach ($children as $child) {
                        $text .= "\n" . __d('baser_core', "コンテンツ: {0}を削除しました。", $child->title);
                    }
                }
                $message = $text;
                $this->BcMessage->setSuccess($message, true, false);
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
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
            $trash = $service->getTrashIndex($this->request->getQueryParams())->orderBy(['plugin', 'type']);
            foreach ($trash as $entity) {
                if (!$service->hardDeleteWithAssoc($entity->id)) $result = false;
            }
            $message = __d('baser_core', 'ゴミ箱を空にしました。');
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
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
        $this->request->allowMethod(['post', 'put', 'patch']);
        $content = $errors = null;
        try {
            $content = $service->update($service->get($id, ['contain' => []]), $this->request->getData());
            $message = __d('baser_core', 'コンテンツ「{0}」を更新しました。', $content->title);
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
        $this->request->allowMethod(['post', 'put', 'patch']);
        $restored = null;
        try {
            if ($restored = $service->restore($id)) {
                $message = __d('baser_core', 'ゴミ箱: {0} を元に戻しました。', $restored->title);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'ゴミ箱の復元に失敗しました');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
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

        $content = $errors = $message = null;
        $result = false;
        if ($id && $status) {
            try {
                switch ($status) {
                    case 'publish':
                        $content = $service->publish($id);
                        $message = __d('baser_core', 'コンテンツ: {0} を公開しました。', $content->title);
                        $this->BcMessage->setSuccess($message, true, false);
                        break;
                    case 'unpublish':
                        $content = $service->unpublish($id);
                        $message = __d('baser_core', 'コンテンツ: {0} を非公開にしました。', $content->title);
                        $this->BcMessage->setSuccess($message, true, false);
                        break;
                }
                $result = true;
            } catch (PersistenceFailedException $e) {
                $errors = $e->getEntity()->getErrors();
                $message = __d('baser_core', "入力エラーです。内容を修正してください。");
                $this->setResponse($this->response->withStatus(400));
            } catch (\Throwable $e) {
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '無効な処理です。') . __d('baser_core', "データが不足しています");
        }

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
            $this->set(['message' => __d('baser_core', '無効な処理です。')]);
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
     * @param ContentsService $service
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
            $oldTitle = $oldContent->title;
            $newContent = $service->rename($oldContent, $this->getRequest()->getData());
            $url = $service->getUrlById($newContent->id);
            $this->setResponse($this->response->withStatus(200));
            $message = __d('baser_core', '「{0}」を「{1}」に名称変更しました。',
                $oldTitle,
                $newContent->title
            );
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
     * @param ContentsServiceInterface|ContentsService $service
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
            $message = __d('baser_core', '{0} を作成しました。', $alias->title);
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
     * @param ContentsService $service
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
                $beforeContent = $service->get($this->request->getData('origin.id'));
                $beforeUrl = $beforeContent->url;
                $content = $service->move($this->request->getData('origin'), $this->request->getData('target'));
                $message = sprintf(
                    __d('baser_core', "コンテンツ「%s」の配置を移動しました。\n%s > %s"),
                    $content->title,
                    rawurldecode($beforeUrl),
                    rawurldecode($content->url)
                );
                $url = $service->getUrlById($content->id, true);
                $this->BcMessage->setSuccess($message, true, false);
            } catch (PersistenceFailedException $e) {
                $errors = $e->getEntity()->getErrors();
                $message = __d('baser_core', "入力エラーです。内容を修正してください。");
                $this->setResponse($this->response->withStatus(400));
            } catch (\Throwable $e) {
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
        } else {
            $message = __d('baser_core', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。');
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
            'publish' => __d('baser_core', '公開'),
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
                sprintf(__d('baser_core', 'コンテンツ 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set(['message' => $message, 'errors' => $errors]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

}
