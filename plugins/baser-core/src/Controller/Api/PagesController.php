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

use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PagesService;
use BaserCore\Service\PagesServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class PagesController
 * @uses PagesController
 */
class PagesController extends BcApiController
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
     * 固定ページ一覧取得
     * @param PagesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PagesServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $this->set([
            'pages' => $this->paginate($service->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['pages']);
    }

    /**
     * 固定ページ取得
     *
     * クエリーパラーメーター
     * - status: string 公開ステータス（初期値：publish）
     *  - `publish` 公開されたページ
     *  - `` 全て
     *
     * @param PagesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(PagesServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if(isset($queryParams['status'])) {
            if(!$this->Authentication->getIdentity()) throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $page = $message = null;
        try {
            $page = $service->get($id, $queryParams);
        } catch(\Exception $e) {
            $this->setResponse($this->response->withStatus(401));
            $message = $e->getMessage();
        }

        $this->set([
            'page' => $page,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'message']);
    }

    /**
     * 固定ページ登録
     * @param PagesServiceInterface $service
     * @checked
     * @unitTest
     * @noTodo
     */
    public function add(PagesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $page = $service->create($this->request->getData());
            $message = __d('baser', '固定ページ「{0}」を追加しました。', $page->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $page = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'page' => $page,
            'content' => $page->content,
            'message' => $message,
            'errors' => $page->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'page',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * 固定ページ削除
     * @param PagesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PagesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['delete']);
        $page = $service->get($id);
        try {
            if ($service->delete($id)) {
                $message = __d('baser', '固定ページ: {0} をゴミ箱に移動しました。', $page->content->title);
            }
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $page = $e->getEntity();
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'page' => $page
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'message']);
    }

    /**
     * 固定ページ情報編集
     * @param PagesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(PagesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $page = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', '固定ページ 「{0}」を更新しました。', $page->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $page = $e->getEntity();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'page' => $page,
            'content' => $page->content,
            'errors' => $page->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'message', 'errors']);
    }

    /**
     * コピー
     * @param PagesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(PagesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            /* @var PagesService $service */
            $page = $service->copy($this->request->getData());
            $message = __d('baser', '固定ページのコピー「%s」を追加しました。', $page->content->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(500));
            $page = $e->getEntity();
            $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
        }
        $this->set([
            'message' => $message,
            'page' => $page,
            'content' => $page->content,
            'errors' => $page->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'content', 'message', 'errors']);
    }

}
