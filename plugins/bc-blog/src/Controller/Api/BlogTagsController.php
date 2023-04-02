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
use BcBlog\Service\BlogTagsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogTagsController
 */
class BlogTagsController extends BcApiController
{

    /**
     * [API] ブログタグ一覧取得
     *
     * @param BlogTagsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogTagsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $queryParams = array_merge([
            'contain' => null,
        ], $this->getRequest()->getQueryParams());
        $this->set([
            'blogTags' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTags']);
    }

    /**
     * [API] 単一ブログタグー取得
     *
     * @param BlogTagsServiceInterface $service
     * @param $blogTagId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogTagsServiceInterface $service, $blogTagId)
    {
        $this->request->allowMethod(['get']);
        $blogTag = $message = null;
        try {
            $blogTag = $service->get($blogTagId);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogTag' => $blogTag,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTag', 'message']);
    }

}
