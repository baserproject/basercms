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

namespace BcCustomContent\Controller\Admin;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Service\Admin\CustomFieldsAdminServiceInterface;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomFieldsController
 */
class CustomFieldsController extends CustomContentAdminAppController
{

    /**
     * カスタムフィールドの一覧を表示
     *
     * @param CustomFieldsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomFieldsAdminServiceInterface $service)
    {
        $this->set(['entities' => $service->getIndex($this->getRequest()->getQueryParams())]);
    }

    /**
     * カスタムフィールドの新規追加
     *
     * @param CustomFieldsAdminServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomFieldsAdminServiceInterface $service)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT CustomFields.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->create($this->getRequest()->getData());
                // EVENT CustomFields.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'フィールド「{0}」を追加しました', $entity->title));
                $this->redirect(['action' => 'edit', $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set([
            'entity' => $entity?? $service->getNew()
        ]);
    }

    /**
     * カスタムフィールドの編集
     *
     * @param CustomFieldsAdminServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomFieldsAdminServiceInterface $service, int $id)
    {
        $entity = $service->get($id);
        if ($this->getRequest()->is(['post', 'put'])) {
            // EVENT CustomFields.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                // EVENT CustomFields.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'フィールド「{0}」を更新しました。', $entity->title));
                return $this->redirect(['action' => 'edit', $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set([
            'entity' => $entity
        ]);
    }

    /**
     * カスタムフィールドの削除
     *
     * @param CustomFieldsServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomFieldsServiceInterface $service, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'フィールド「{0}」を削除しました。', $entity->title));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
