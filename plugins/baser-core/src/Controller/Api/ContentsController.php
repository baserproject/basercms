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

use Exception;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Service\ContentService;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Service\ContentServiceInterface;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('contentId');
        $content = $contentService->get($id);
        $children = $contentService->getChildren($id);
        try {
            if($contentService->deleteRecursive($id)) {
                $text = "コンテンツ: " . $content->name . "を削除しました。";
                if ($children) {
                    $content = array_merge([$content], $children->toArray());
                    foreach ($children as $child) {
                        $text .= "\nコンテンツ: " . $child->name . "を削除しました。";
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
        $trash = $contentService->getTrashIndex($this->request->getQueryParams())->order(['plugin', 'type']);
        $text = "ゴミ箱: ";
        try {
            foreach ($trash as $entity) {
                if ($contentService->hardDeleteWithAssoc($entity->id)) {
                    $text .=  "$entity->name($entity->type)" . "を削除しました。";
                    }
            }
            $message = __d('baser', $text);
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
     * @param ContentServiceInterface $contentService
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function change_status(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        if ($this->request->getData('id') && $this->request->getData('status')) {
            try {
                switch($this->request->getData('status')) {
                    case 'publish':
                        $content = $contentService->publish($this->request->getData('id'));
                        $message = __d('baser', 'コンテンツ: {0} を公開しました。', $content->name);
                        break;
                    case 'unpublish':
                        $content = $contentService->unpublish($this->request->getData('id'));
                        $message = __d('baser', 'コンテンツ: {0} を非公開にしました。', $content->name);
                        break;
                }
            } catch (\Exception $e) {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser',  '無効な処理です。') . "データが不足しています";
        }
        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }

    /**
     * get_full_url
     *
     * @param  ContentServiceInterface $contentService
     * @param  int $id
     * @checked
     * @unitTest
     * @note(value="TODO内容を荒川さんに確認")
     */
    public function get_full_url(ContentServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['get']);
        if ($id) {
            // TODO ucmitz: bcTreeからgetUrlByIdする際に値がおかしくなるので、getUrlById自体を修正する
            $this->set(['fullUrl' => $contentService->getUrlById($id, true)]);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $this->set(['message' => __d('baser',  '無効な処理です。')]);
        }
        $this->viewBuilder()->setOption('serialize', ['message', 'fullUrl']);
    }

    /**
     * 指定したIDのコンテンツが存在するか確認する
     * ゴミ箱のものは無視
     *
     * @param  ContentServiceInterface $contentService
     * @param $id
     * @return Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function exists(ContentServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['get']);
        $this->set(['exists' => $contentService->exists($id)]);
        $this->viewBuilder()->setOption('serialize', ['exists']);
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     * @param ContentServiceInterface $contentService
     * @param int $siteId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_content_folder_list(ContentServiceInterface $contentService, $siteId)
    {
        $this->request->allowMethod(['get']);
        $this->set(['list' => $contentService->getContentFolderList($siteId,['conditions' => ['site_root' => false]])]);
        $this->viewBuilder()->setOption('serialize', ['list']);
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rename(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        if (empty($this->request->getData('id')) || empty($this->request->getData('title'))) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', '無効な処理です。');
        }else {
            $oldContent = $contentService->get($this->request->getData('id'));
            $oldTitle = $oldContent->title;
            $newContent = $contentService->update($oldContent, ['title' => $this->request->getData('title')]);
            if ($newContent->hasErrors()) {
                $this->setResponse($this->response->withStatus(500));
                $message = $newContent->getErrors();
            } else {
                $this->setResponse($this->response->withStatus(200));
                $url = $contentService->getUrlById($this->request->getData('title'));
                $this->set(['url' => $url]);
                $message = sprintf(
                    '%s%s',
                    Configure::read(
                        sprintf(
                            'BcContents.items.%s.%s.title',
                            $newContent->plugin,
                            $newContent->type
                        )
                    ),
                    sprintf(
                        __d('baser', '「%s」を「%s」に名称変更しました。'),
                        $oldTitle,
                        $newContent->title
                    )
                );
            }
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'url']);
    }

    /**
     * add_alias
     *
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add_alias(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post']);
        try {
            $alias = $contentService->alias($this->request->getData('aliasId'), $this->request->getData('content'));
            if(!$alias->hasErrors()) {
                $message = __d('baser', '{0} を作成しました。', $alias->name);
                $this->set('content', $alias);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', '保存中にエラーが発生しました。');
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', "無効な処理です。\n" . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content']);
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     * @param  ContentServiceInterface $contentService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function exists_content_by_url(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post']);
        if (!$this->request->getData('url')) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', "無効な処理です。");
        } else {
            if ($contentService->existsContentByUrl($this->request->getData('url'))) {
                $this->setResponse($this->response->withStatus(200));
            } else {
                $this->setResponse($this->response->withStatus(404));
                $message = __d('baser', "データが見つかりません");
            }
        }
        if(!empty($message)) $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * 並び順を移動する
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move(ContentServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $siteConfig = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
        if (empty($this->request->getData())) {
            $message = __d('baser', '無効な処理です。');
            $this->setResponse($this->response->withStatus(400));
        } elseif(!$contentService->exists($this->request->getData('origin.id'))) {
            $message = __d('baser', 'データが存在しません。');
            $this->setResponse($this->response->withStatus(500));
        } elseif($siteConfig->isChangedContentsSortLastModified($this->request->getData('listDisplayed'))) {
            $message = __d('baser', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。');
            $this->setResponse($this->response->withStatus(500));
        } elseif (!$contentService->isMovable($this->request->getData('origin.id'), $this->request->getData('target.parentId'))) {
            $message = __d('baser', '同一URLのコンテンツが存在するため処理に失敗しました。（現在のサイトに存在しない場合は、関連サイトに存在します）');
            $this->setResponse($this->response->withStatus(500));;
        } else {
            // 正常系
            $message = $this->execMove($contentService, $siteConfig);
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'url', 'content']);
    }

    /**
     * execMove
     *
     * @param  ContentService $contentService
     * @param  SiteConfigsTable $siteConfig
     * @return string
     */
    protected function execMove($contentService, $siteConfig)
    {
            // EVENT Contents.beforeMove
            $beforeEvent = $this->dispatchLayerEvent('beforeMove', [
                'request' => $this->request
            ]);
            if ($beforeEvent !== false) {
                $this->request = ($beforeEvent->getResult() === null || $beforeEvent->getResult() === true)? $beforeEvent->getData('request') : $beforeEvent->getResult();
            }
            $content = $contentService->get($this->request->getData('origin.id'));
            $beforeUrl = $content->url;
            try {
                $result = $contentService->move($this->request->getData('origin'), $this->request->getData('target'));
                if ($this->request->getData('origin.parentId') == $this->request->getData('target.parentId')) {
                    // 親が違う場合は、Contentモデルで更新してくれるが同じ場合更新しない仕様のためここで更新する
                    $siteConfig->updateContentsSortLastModified();
                }
                // EVENT Contents.afterMove
                $afterEvent = $this->dispatchLayerEvent('afterMove', [
                    'result' => $result
                ]);
                if ($afterEvent !== false) {
                    $this->request = ($afterEvent->getResult() === null || $afterEvent->getResult() === true)? $afterEvent->getData('result') : $afterEvent->getResult();
                }
                $message = sprintf(__d('baser', "コンテンツ「%s」の配置を移動しました。\n%s > %s"), $result->title, urldecode($beforeUrl), urldecode($result->url));
                $url = $contentService->getUrlById($result->id, true);
                $this->set(['url' => $url]);
                $this->set(['content' => $result]);
            } catch(Exception $e) {
                $message = __d('baser', 'データ保存中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
            return $message;
    }
}
