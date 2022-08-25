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
use BaserCore\Service\SiteConfigsServiceInterface;
use BcSearchIndex\Service\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Event\EventInterface;

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
     */
    public function index(
        SearchIndexesServiceInterface $service,
        SearchIndexesAdminServiceInterface $adminService,
        SiteConfigsServiceInterface $siteConfigService)
    {
        $this->setViewConditions('User', ['default' => [
            'query' => [
                'limit' => $siteConfigService->getValue('admin_list_num'),
                'sort' => 'id',
                'direction' => 'asc',
            ],
            'SearchIndex' => ['site_id' => 0]
        ]]);

        // EVENT SearchIndex.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->getRequest()
        ]);
        if ($event !== false) {
            $this->setRequest(($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult());
        }

        $this->set($adminService->getViewVarsForIndex(
            $this->paginate($service->getIndex($this->getRequest()->getQueryParams())),
            $this->getRequest()
        ));
    }

    /**
     * [ADMIN] 検索インデックス削除　(ajax)
     *
     * @param int $id
     * @return    void
     * @access    public
     */
    public function delete($id = null)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        /* 削除処理 */
        if ($this->SearchIndex->delete($id)) {
            $message = sprintf(__d('baser', '検索インデックスより NO.%s を削除しました。'), $id);
            $this->SearchIndex->saveDbLog($message);
            exit(true);
        }
        exit();
    }

    /**
     * [ADMIN] 検索インデックス一括削除
     *
     * @param $ids
     * @return bool
     * @access    public
     */
    protected function _batch_del($ids)
    {
        if (!$ids) {
            return true;
        }
        foreach($ids as $id) {
            /* 削除処理 */
            if ($this->SearchIndex->delete($id)) {
                $message = sprintf(__d('baser', '検索インデックスより NO.%s を削除しました。'), $id);
                $this->SearchIndex->saveDbLog($message);
            }
        }
        return true;
    }

    /**
     * 検索インデックスを再構築する
     */
    public function reconstruct()
    {
        set_time_limit(0);
        if ($this->SearchIndex->reconstruct()) {
            $this->BcMessage->setSuccess('検索インデックスの再構築に成功しました。');
        } else {
            $this->BcMessage->setError('検索インデックスの再構築に失敗しました。');
        }
        $this->redirect(['action' => 'index']);
    }
}
