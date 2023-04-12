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

use BaserCore\Service\ContentFoldersServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentFoldersController
 */
class ContentFoldersController extends BcApiController
{

    /**
     * コンテンツフォルダ一覧取得
     * @param ContentFoldersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentFoldersServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }
        $queryParams = array_merge([
            'status' => 'publish',
            'contain' => null,
        ], $queryParams);

        $this->set([
            'contentFolders' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolders']);
    }

    /**
     * コンテンツフォルダ取得
     * @param ContentFoldersServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentFoldersServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $contentFolder = $message = null;
        try {
            $contentFolder = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'contentFolder' => $contentFolder,
            'content' => $contentFolder?->content,
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentFolder', 'content', 'message']);
    }

}
