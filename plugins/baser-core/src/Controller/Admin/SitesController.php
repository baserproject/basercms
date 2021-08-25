<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use BaserCore\Service\SitesServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SitesController
 * @package BaserCore\Controller\Admin
 */
class SitesController extends BcAdminAppController
{

    /**
     * サイト一覧
     * @param SitesServiceInterface $sitesService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SitesServiceInterface $sitesService)
    {
        $this->setViewConditions('Site', ['default' => ['query' => [
            'num' => $sitesService->getSiteConfig('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Sites.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->request
        ]);
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }

        $this->set('sites', $this->paginate($sitesService->getIndex($this->request->getQueryParams())));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * サイト追加
     *
     * @checked
     * @unitTest
     */
    public function add(SitesServiceInterface $sitesService)
    {
        if ($this->request->is('post')) {

            /*** Sites.beforeAdd ** */
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }

            $site = $sitesService->create($this->request->getData());
            if (!$site->getErrors()) {
                /*** Sites.afterAdd ***/
                $this->dispatchLayerEvent('afterAdd', [
                    'site' => $site
                ]);

                // TODO 未実装のためコメントアウト
                /* >>>
                if (!empty($site->theme)) {
                    $this->BcManager->installThemesPlugins($site->theme);
                }
                <<< */

                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を追加しました。'), $site->display_name));
                return $this->redirect(['action' => 'edit', $site->id]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $site = $sitesService->getNew();
        }
        $this->set('site', $site);
    }

    /**
     * サイト情報編集
     *
     * @param $id
     * @checked
     * @unitTest
     */
    public function edit(SitesServiceInterface $sitesService, $id)
    {
        if (!$id) {
            $this->notFound();
        }
        $site = $sitesService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {

            /*** Sites.beforeEdit ** */
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }

            $beforeSite = clone $site;
            $site = $sitesService->update($site, $this->request->getData());
            if (!$site->getErrors()) {

                /*** Sites.afterEdit ***/
                $this->dispatchLayerEvent('afterEdit', [
                    'site' => $site
                ]);

                // TODO 未実装のためコメントアウト
                /* >>>
                if (!empty($site->theme) && $beforeSite->theme !== $site->theme) {
                    $this->BcManager->installThemesPlugins($site->theme);
                }
                <<< */

                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を更新しました。'), $site->display_name));
                $this->redirect(['action' => 'edit', $id]);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set('site', $site);
    }

    /**
     * 削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SitesServiceInterface $sitesService, $id)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $site = $sitesService->get($id);
        try {
            if ($sitesService->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'サイト: {0} を削除しました。', $site->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
