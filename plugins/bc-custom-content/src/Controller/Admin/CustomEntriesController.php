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

use BaserCore\Error\BcException;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BcCustomContent\Service\Admin\CustomEntriesAdminServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomEntriesController
 */
class CustomEntriesController extends CustomContentAdminAppController
{

    /**
     * Before Filter
     *
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $tableId = $this->request->getParam('pass.0');
        if(!$tableId) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            return $this->redirect(['plugin' => 'BaserCore', 'controller' => 'Contents', 'action' => 'index']);
        }

        /** @var CustomTablesService $entriesService */
        $tablesService = $this->getService(CustomTablesServiceInterface::class);
        $contentId = $tablesService->getCustomContentId($tableId);

        if($contentId) {
            /** @var ContentsService $contentsService */
            $contentsService = $this->getService(ContentsServiceInterface::class);
            $request = $contentsService->setCurrentToRequest(
                'BcCustomContent.CustomContent',
                $contentId,
                $this->getRequest()
            );
            if (!$request) throw new BcException(__d('baser_core', 'コンテンツデータが見つかりません。'));
            $this->setRequest($request);
        }
    }

    /**
     * カスタムエントリーの一覧を表示する
     *
     * @param CustomEntriesAdminServiceInterface $service
     * @param int $tableId
     */
    public function index(CustomEntriesAdminServiceInterface $service, int $tableId)
    {
        $service->setup($tableId);
        $this->setViewConditions('CustomEntry', [
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
                    'sort' => 'id',
                    'direction' => 'desc',
                ]]]);

        $table = $service->getTableWithLinksByAll($tableId);
        if($table->has_child) {
            $entries = $service->getTreeIndex($this->getRequest()->getQueryParams());
        } else {
            $entries = $this->paginate($service->getIndex($this->getRequest()->getQueryParams()));
        }

        $this->set($service->getViewVarsForIndex($table, $entries));

        $this->setRequest($this->getRequest()->withParsedBody($this->getRequest()->getQueryParams()));
    }

    /**
     * カスタムエントリーの新規追加
     *
     * @param CustomEntriesAdminServiceInterface $service
     * @param int $tableId
     */
    public function add(CustomEntriesAdminServiceInterface $service, int $tableId)
    {
        $service->setup($tableId, $this->getRequest()->getData());
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->create($this->getRequest()->getData());
                $entity = $service->get($entity->id);
                $this->BcMessage->setSuccess(__d('baser_core',
                    'エントリー「{0}」を追加しました。',
                    $entity->{$entity->custom_table->display_field}
                ));
                return $this->redirect(['action' => 'edit', $tableId, $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForAdd($tableId, $entity?? $service->getNew($tableId)));
    }

    /**
     * カスタムエントリーの編集
     *
     * @param CustomEntriesAdminServiceInterface $service
     * @param int $tableId
     * @param int $id
     */
    public function edit(CustomEntriesAdminServiceInterface $service, int $tableId, int $id)
    {
        $service->setup($tableId, $this->getRequest()->getData());
        $entity = $service->get($id);
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser_core',
                    'エントリー「{0}」を更新しました。',
                    $entity->{$entity->custom_table->display_field}
                ));
                return $this->redirect(['action' => 'edit', $tableId, $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForEdit($tableId, $entity));
    }


    /**
     * カスタムエントリーの削除
     *
     * @param CustomEntriesServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     */
    public function delete(CustomEntriesServiceInterface $service, int $tableId, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $service->setup($tableId);
        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core',
                    'エントリー「{0}」を削除しました。',
                    $entity->{$entity->custom_table->display_field}
                ));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $tableId]);
    }

    /**
     * カスタムエントリーを上に移動
     *
     * @param CustomEntriesServiceInterface $service
     * @param int $tableId
     * @param int $id
     */
    public function move_up(CustomEntriesServiceInterface $service, int $tableId, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put', 'delete']);
        $service->setup($tableId);
        try {
            $entity = $service->get($id);
            if ($service->moveUp($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'エントリー「{0}」を上に移動しました。', $entity->title));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        $this->redirect([
            'controller' => 'CustomEntries',
            'action' => 'index',
            $tableId
        ]);
    }

    /**
     * カスタムエントリーを下に移動
     *
     * @param CustomEntriesServiceInterface $service
     * @param int $tableId
     * @param int $id
     */
    public function move_down(CustomEntriesServiceInterface $service, int $tableId, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put', 'delete']);
        $service->setup($tableId);
        try {
            $entity = $service->get($id);
            if ($service->moveDown($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'エントリー「{0}」を下に移動しました。', $entity->title));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        $this->redirect([
            'controller' => 'CustomEntries',
            'action' => 'index',
            $tableId
        ]);
    }

}
