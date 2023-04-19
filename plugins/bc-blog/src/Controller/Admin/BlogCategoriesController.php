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

use BcBlog\Model\Entity\BlogCategory;
use BcBlog\Service\Admin\BlogCategoriesAdminServiceInterface;
use BcBlog\Service\BlogCategoriesServiceInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * カテゴリコントローラー
 * @uses BlogCategoriesController
 */
class BlogCategoriesController extends BlogAdminAppController
{

    /**
     * [ADMIN] ブログカテゴリを一覧表示する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(
        BlogCategoriesAdminServiceInterface $service,
        int $blogContentId
    )
    {
        $this->set($service->getViewVarsForIndex($blogContentId));
    }

    /**
     * [ADMIN] 登録処理
     *
     * @param BlogCategoriesAdminServiceInterface $service
     * @param string $blogContentId
     * @return Response|null|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(BlogCategoriesAdminServiceInterface $service, string $blogContentId)
    {
        if ($this->request->is('post')) {
            // EVENT BlogCategories.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                /* @var BlogCategory $blogCategory */
                $blogCategory = $service->create($blogContentId, $this->request->getData());
                // EVENT BlogCategory.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'blogCategory' => $blogCategory
                ]);
                $this->BcMessage->setSuccess(sprintf(
                    __d('baser_core', 'カテゴリー「%s」を追加しました。'),
                    $blogCategory->name
                ));
                return $this->redirect(['action' => 'index', $blogContentId]);
            } catch (PersistenceFailedException $e) {
                $blogCategory = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($service->getViewVarsForAdd(
            $blogContentId,
            $blogCategory ?? $service->getNew($blogContentId)
        ));
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param BlogCategoriesAdminServiceInterface $service
     * @param int $blogContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(BlogCategoriesAdminServiceInterface $service, int $blogContentId, int $id)
    {
        $blogCategory = $service->get($id);
        if ($this->request->is('put')) {
            // EVENT BlogCategories.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                /* @var BlogCategory $blogCategory */
                $blogCategory = $service->update($blogCategory, $this->request->getData());
                // EVENT BlogCategory.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'blogCategory' => $blogCategory
                ]);
                $this->BcMessage->setSuccess(sprintf(
                    __d('baser_core', 'カテゴリー「%s」を更新しました。'),
                    $blogCategory->name
                ));
                $this->redirect(['action' => 'index', $blogContentId]);
            } catch (PersistenceFailedException $e) {
                $blogCategory = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($service->getViewVarsForEdit(
            $blogContentId,
            $blogCategory
        ));
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param BlogCategoriesServiceInterface $service
     * @param int $blogContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogCategoriesServiceInterface $service, int $blogContentId, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        /* @var BlogCategory $blogCategory */
        $blogCategory = $service->get($id);
        if ($service->delete($id)) {
            $this->BcMessage->setSuccess(sprintf(
                __d('baser_core', '%s を削除しました。'),
                $blogCategory->name
            ));
        } else {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
        }
        $this->redirect(['action' => 'index', $blogContentId]);
    }

}
