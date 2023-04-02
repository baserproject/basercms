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
use BcBlog\Service\BlogCommentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogCommentsController
 */
class BlogCommentsController extends BcApiController
{

    /**
     * [API] ブログコメント一覧取得
     *
     * @param BlogCommentsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogCommentsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish',
            'contain' => null,
        ], $queryParams);

        $this->set([
            'blogComments' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComments']);
    }

    /**
     * [API] 単一ブログコメントー取得
     *
     * @param BlogCommentsServiceInterface $service
     * @param $blogCommentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogCommentsServiceInterface $service, $blogCommentId)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'status' => 'publish'
        ], $queryParams);

        $blogComment = $message = null;
        try {
            $blogComment = $service->get($blogCommentId, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogComment' => $blogComment,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComment', 'message']);
    }

}
