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

use BaserCore\Service\ContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        try {
            $content = $service->get($id, $queryParams);
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
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $params = array_merge([
            'list_type' => 'index',
            'contain' => null,
            'status' => 'publish'
        ], $queryParams);
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

}
