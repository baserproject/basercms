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

namespace BcUploader\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcUploader\Service\UploaderCategoriesService;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * アップロードカテゴリコントローラー
 */
class UploaderCategoriesController extends BcAdminAppController
{

    /**
     * ファイルカテゴリ一覧
     *
     * @param UploaderCategoriesService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function index(UploaderCategoriesServiceInterface $service)
    {
        $this->set(['uploaderCategories' => $this->paginate($service->getIndex())]);
    }

    /**
     * 新規登録
     *
     * @param UploaderCategoriesService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function add(UploaderCategoriesServiceInterface $service)
    {
        if ($this->getRequest()->is(['post', 'put'])) {

            // EVENT UploaderCategories.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }

            try {
                $entity = $service->create($this->getRequest()->getData());
                // EVENT UploaderCategories.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'アップロードカテゴリ「{0}」を追加しました。', $entity->name));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', "入力エラーです。内容を修正してください。"));
            } catch (Throwable $e) {
                $this->setResponse($this->response->withStatus(500));
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set(['uploaderCategory' => $entity ?? $service->getNew()]);
    }

    /**
     * 編集
     *
     * @param UploaderCategoriesService $service
     * @param $id
     * @return void
     * @checked
     * @noTodo
     */
    public function edit(UploaderCategoriesServiceInterface $service, $id)
    {
        $entity = $service->get($id);
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT UploaderCategories.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                // EVENT UploaderCategories.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'アップロードカテゴリ「{0}」を更新しました。', $entity->name));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', "入力エラーです。内容を修正してください。"));
            } catch (Throwable $e) {
                $this->setResponse($this->response->withStatus(500));
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set(['uploaderCategory' => $entity]);
    }

    /**
     * 削除
     *
     * @param int $id
     * @return    void
     * @checked
     * @noTodo
     */
    public function delete(UploaderCategoriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entity = $service->get($id);
        try {
            if($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'アップロードカテゴリ「{0}」を削除しました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * [ADMIN] コピー
     *
     * @param UploaderCategoriesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     */
    public function copy(UploaderCategoriesServiceInterface $service, $id)
    {
        try {
            if($service->copy($id)) {
                $entity = $service->get($id);
                $this->BcMessage->setSuccess(__d('baser_core', 'アップロードカテゴリ「{0}」をコピーしました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }
}
