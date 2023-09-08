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
use BcCustomContent\Service\Admin\CustomTablesAdminServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomTablesController
 */
class CustomTablesController extends CustomContentAdminAppController
{

    /**
     * before filter
     *
     * 削除処理について、レイアウト上の問題にて、vue.js のアプリケーション内に削除ボタンを配置する必要があるが、
     * アプリケーション内では、FormHelper::postLink() の JS が無効化されてしまうため、vue.js 内で実装。
     * _Token フィールドの生成が JS上で難しいため、validatePost を false にしている
     * @see plugins/bc-admin-third/src/bc_custom_content/js/admin/custom_tables/form.js
     *
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        if ($this->request->getParam('action') === 'delete') {
            $this->Security->setConfig('validatePost', false);
        }
        return parent::beforeFilter($event);
    }

    /**
     * カスタムテーブルの一覧を表示
     *
     * @param CustomTablesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomTablesAdminServiceInterface $service)
    {
        $this->set(['entities' => $service->getIndex($this->getRequest()->getQueryParams())]);
    }

    /**
     * カスタムテーブルの新規追加
     *
     * @param CustomTablesAdminServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomTablesAdminServiceInterface $service)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT CustomTables.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->create($this->getRequest()->getData());
                // EVENT CustomTables.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'テーブル「{0}」を追加しました', $entity->title));
                $this->redirect(['action' => 'edit', $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set([
            'entity' => $entity?? $service->getNew(),
            'flatLinks' => []
        ]);
    }

    /**
     * カスタムテーブルの編集
     *
     * @param CustomTablesAdminServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomTablesAdminServiceInterface $service, int $id)
    {
        $entity = $service->get($id, ['contain' => ['CustomLinks' => ['CustomFields']]]);
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT CustomTables.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                // EVENT CustomTables.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'テーブル「{0}」を更新しました', $entity->title));
                return $this->redirect(['action' => 'edit', $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForEdit($entity));
    }

    /**
     * カスタムテーブルの削除
     *
     * @param CustomTablesServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @noTodo
     * @checked
     * @unitTest
     */
    public function delete(CustomTablesServiceInterface $service, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'テーブル「{0}」を削除しました。', $entity->title));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $id]);
    }

}
