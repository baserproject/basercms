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

use BaserCore\Service\PagesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PagesController
 * @uses \BaserCore\Controller\Api\Admin\PagesController
 */
class PagesController extends BcApiController
{

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
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish',
            'contain' => null
        ], $queryParams);

        $this->set([
            'pages' => $this->paginate($service->getIndex($queryParams))
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
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $page = $message = null;
        try {
            $page = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'page' => $page,
            'content' => ($page) ? $page->content : null,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'content', 'message']);
    }

}
