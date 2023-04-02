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

namespace BcContentLink\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcContentLink\Service\ContentLinksService;
use BcContentLink\Service\ContentLinksServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 */
class ContentLinksController extends BcApiController
{

    /**
     * initialize
     * @return void
     * @checked
     * @unitTest
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['view']);
    }

    /**
     * コンテンツリンクを登録する（認証要）
     *
     * /baser/api/bc-content-link/content_links/add.json
     *
     * ### POSTデータ
     * - content
     *  - parent_id: 親のコンテンツID
     *  - title: タイトル
     *  - plugin: プラグイン名
     *  - type: コンテンツタイプ名
     *  - site_id: サイトID
     *
     * ### レスポンス
     * - contentLink: コンテンツリンクエンティティ
     * - content: コンテンツエンティティ
     * - message: メッセージ
     * - errors: エラーが発生した場合の詳細内容
     *
     * @param ContentLinksService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function add(ContentLinksServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $entity = $content = $errors = null;
        try {
            $entity = $service->create($this->request->getData());
            $content = $entity->content;
            $message = __d('baser_core', 'リンク「{0}」を追加しました。', $entity->content->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'contentLink' => $entity,
            'content' => $content,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'contentLink',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * コンテンツリンクを編集（認証要）
     *
     * /baser/api/bc-content-link/content_links/edit/{id}.json
     *
     * ### POSTデータ
     *  - id: コンテンツリンクID
     * - コンテンツリンクデータ
     *
     * ### レスポンス
     * - contentLink: コンテンツリンクエンティティ
     * - message: メッセージ
     * - errors: エラーが発生した場合の詳細内容
     *
     * @param ContentLinksServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $contentLink = $errors = null;

        try {
            $contentLink = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'コンテンツリンク: 「{0}」を更新しました。', $contentLink->content->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'contentLink' => $contentLink,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'contentLink', 'errors']);
    }

    /**
     * コンテンツリンクを削除（認証要）
     *
     * /baser/api/bc-content-link/content_links/delete/{id}.json
     *
     * ### POSTデータ
     *  - id: コンテンツリンクID
     *
     * ### レスポンス
     * - contentLink: コンテンツリンクエンティティ
     * - message: メッセージ
     * - errors: エラーが発生した場合の詳細内容
     *
     * @param ContentLinksServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $contentLink = null;
        try {
            $contentLink = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'コンテンツリンク: {0} を削除しました。', $contentLink->content->title);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'contentLink' => $contentLink
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'contentLink']);
    }

    /**
     * コンテンツリンク取得
     *
     * /baser/api/bc-content-link/content_links/view/{id}.json
     *
     * クエリーパラーメーター
     * - status: string 公開ステータス（初期値：publish）
     *  - `publish` 公開されたページ
     *  - `` 全て
     *
     * @param ContentLinksServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            if (!$this->isAdminApiEnabled()) throw new ForbiddenException();
        }

        $queryParams = array_merge($queryParams, [
            'status' => 'publish'
        ]);

        $contentLink = $message = null;
        try {
            $contentLink = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'contentLink' => $contentLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentLink', 'message']);
    }
}
