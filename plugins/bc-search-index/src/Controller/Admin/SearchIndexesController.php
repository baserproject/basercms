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

namespace BcSearchIndex\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Error\BcException;
use BaserCore\Service\SiteConfigsServiceInterface;
use BcSearchIndex\Service\Admin\SearchIndexesAdminService;
use BcSearchIndex\Service\Admin\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SearchIndexesController
 *
 * 検索インデックスコントローラー
 */
class SearchIndexesController extends BcAdminAppController
{

    /**
     * before render
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->viewBuilder()->addHelpers(['BcSearchIndex.BcSearchIndex']);
    }

    /**
     * [ADMIN] 検索インデックス
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(
        SearchIndexesAdminServiceInterface $service,
        SiteConfigsServiceInterface $siteConfigService)
    {
        $currentSite = $this->getRequest()->getAttribute('currentSite');
        $this->setRequest($this->getRequest()->withQueryParams(array_merge(
            $this->getRequest()->getQueryParams(),
            ['site_id' => $currentSite->id]
        )));

        $this->setViewConditions('User', ['default' => [
            'query' => [
                'limit' => $siteConfigService->getValue('admin_list_num'),
                'sort' => 'id',
                'direction' => 'asc',
                'site_id' => 1
            ]
        ]]);

        // EVENT SearchIndexes.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->getRequest()
        ]);
        if ($event !== false) {
            $this->setRequest(($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult());
        }

        /* @var SearchIndexesAdminService $service */
        $this->set($service->getViewVarsForIndex(
            $this->paginate($service->getIndex($this->getRequest()->getQueryParams())),
            $this->getRequest()
        ));
    }

    /**
     * [ADMIN] 検索インデックス削除
     *
     * @param int $id
     * @noTodo
     * @checked
     * @unitTest
     */
    public function delete(SearchIndexesServiceInterface $service, $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        try {
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', '検索インデックスより No.{0} を削除しました。', $id));
            }
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * 検索インデックスを再構築する
     * @noTodo
     * @checked
     * @unitTest
     */
    public function reconstruct(SearchIndexesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        if ($service->reconstruct()) {
            $this->BcMessage->setSuccess('検索インデックスの再構築に成功しました。');
        } else {
            $this->BcMessage->setError('検索インデックスの再構築に失敗しました。');
        }
        $this->redirect(['action' => 'index']);
    }
}
