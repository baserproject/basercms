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

namespace BcBlog\Controller\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcSiteConfig;
use BcBlog\Model\Entity\BlogTag;
use BcBlog\Service\BlogTagsService;
use BcBlog\Service\BlogTagsServiceInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * ブログタグコントローラー
 */
class BlogTagsController extends BlogAdminAppController
{

    /**
     * initialize
     *
     * Admin/BlogTagsControllerの初期化を行う
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', ['entityVarName' => 'blogContent']);
    }

    /**
     * [ADMIN] ブログタグ一覧
     *
     * ブログのタグ一覧を表示する。
     * ページネーションによる遷移先でレコードがなければ１ページ目にリダイレクトする。
     *
     * @param BlogTagsService $service
     * @return void|Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogTagsServiceInterface $service, $blogContentId = [])
    {
        $this->setViewConditions('BlogTag', ['default' => [
            'query' => [
                'limit' => BcSiteConfig::get('admin_list_num'),
                'sort' => 'id',
                'direction' => 'asc'
            ],
        ]]);
        try {
            $blogTags = $this->paginate($service->getIndex($this->getRequest()->getQueryParams()));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index', $blogContentId]);
        }
        $this->set(['blogTags' => $blogTags]);
    }

    /**
     * [ADMIN] ブログタグを登録する
     *
     * ブロブのタグを登録する。
     * 登録に成功した場合、タグの一覧へリダイレクトする。
     *
     * @param BlogTagsService $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogTagsServiceInterface $service)
    {
        if (!empty($this->request->getData())) {
            // EVENT BlogTags.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $blogTag = $service->create($this->request->getData());
                // EVENT BlogTags.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $blogTag
                ]);
                $this->BcMessage->setSuccess(sprintf(
                    __d('baser_core', 'タグ「%s」を追加しました。'),
                    $blogTag->name
                ));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $blogTag = $e->getEntity();
                $this->BcMessage->setError(
                    __d('baser_core', 'エラーが発生しました。内容を確認してください。')
                );
            }
        }
        $this->set(['blogTag' => $blogTag ?? $service->getNew()]);
    }

    /**
     * [ADMIN] ブログタグ編集
     *
     * 指定したブログのタグを編集し、ブログのタグ一覧へリダイレクトする
     *
     * @param BlogTagsService $service
     * @param int $id タグID
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogTagsServiceInterface $service, $id)
    {
        $blogTag = $service->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // EVENT BlogTags.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                /* @var BlogTag $blogTag */
                $blogTag = $service->update($blogTag, $this->getRequest()->getData());
                // EVENT BlogTags.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $blogTag
                ]);
                $this->BcMessage->setSuccess(sprintf(
                    __d('baser_core', 'タグ「%s」を更新しました。'),
                    $blogTag->name
                ));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $blogTag = $e->getEntity();
                $this->BcMessage->setError(
                    __d('baser_core', 'エラーが発生しました。内容を確認してください。')
                );
            }
        }
        $this->set(['blogTag' => $blogTag]);
    }

    /**
     * [ADMIN] ブログタグの削除処理
     *
     * 指定したブログのタグを削除して、ブログのタグ一覧へリダイレクトする。
     *
     * @param BlogTagsService $service
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogTagsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $blogTag = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'ブログタグ「{0}」を削除しました。', $blogTag->name));
            }
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
