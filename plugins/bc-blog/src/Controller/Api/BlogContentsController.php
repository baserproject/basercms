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
namespace BcBlog\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogContentsController
 */
class BlogContentsController extends BcApiController
{

    /**
     * [API] ブログコンテンツー一覧取得
     *
     * @param BlogContentsServiceInterface $blogContentsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogContentsServiceInterface $blogContentsService)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $this->set([
            'blogContents' => $this->paginate($blogContentsService->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContents']);
    }

    /**
     * [API] 単一ブログコンテンツー取得
     *
     * @param BlogContentsServiceInterface $service
     * @param $blogContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogContentsServiceInterface $service, $blogContentId)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status']) || isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogContent = $message = null;
        try {
            $blogContent = $service->get($blogContentId, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogContent' => $blogContent,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogContent', 'message']);
    }

}
