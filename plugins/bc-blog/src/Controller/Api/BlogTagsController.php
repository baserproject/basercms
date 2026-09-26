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
use Cake\Http\Exception\ForbiddenException;

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

        // SQLインジェクション対策(GHSA-636g-rgj6-542v):
        // ガードはリクエスト値の代入後に実施する。代入前だと $queryParams が未定義で
        // isset() が常に false となり、リクエストの contain（ORM内部構造）が素通りして
        // contain[...][fields] 経由で任意 SQL が SELECT 句へ注入される。
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge([
            'contain' => null,
        ], $queryParams);
        // SQLインジェクション対策: ORM内部構造である conditions/order をリクエストから受け付けない
        unset($queryParams['conditions'], $queryParams['order']);
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

        // SQLインジェクション対策(GHSA-636g-rgj6-542v): index() と同様、代入後にガードする。
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['contain'])) {
            throw new ForbiddenException();
        }

        $blogTag = $message = null;
        try {
            $blogTag = $service->get($blogTagId);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'blogTag' => $blogTag,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogTag', 'message']);
    }

}
