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
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 */
class ContentLinksController extends BcApiController
{

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
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser', 'リンク「{0}」を追加しました。', $entity->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'contentLink' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
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
        $this->request->allowMethod(['post', 'put']);
        $contentLink = null;
        $error = null;

        try {
            $contentLink = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'コンテンツリンク: 「{0}」を更新しました。', $contentLink->content->title);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }

        $this->set([
            'message' => $message,
            'contentLink' => $contentLink,
            'errors' => $error,
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
        try {
            $contentLink = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser', 'コンテンツリンク: {0} を削除しました。', $contentLink->content->title);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。');
            }
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $contentLink = $e->getEntity();
            $message = __d('baser', 'データベース処理中にエラーが発生しました。');
        }

        $this->set([
            'message' => $message,
            'contentLink' => $contentLink,
            'errors' => $contentLink->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'contentLink', 'errors']);
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
        if(isset($queryParams['status'])) {
            if(!$this->Authentication->getIdentity()) throw new ForbiddenException();
        }

        $queryParams = array_merge($queryParams, [
            'status' => 'publish'
        ]);

        $contentLink = $message = null;
        try {
            $contentLink = $service->get($id, $queryParams);
        } catch(\Exception $e) {
            $this->setResponse($this->response->withStatus(401));
            $message = $e->getMessage();
        }

        $this->set([
            'contentLink' => $contentLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentLink', 'message']);
    }
}
