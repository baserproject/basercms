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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\Admin\SitesAdminServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class SitesController
 */
class SitesController extends BcAdminAppController
{

    /**
     * サイト一覧
     * @param SitesServiceInterface $siteService
     * @param SiteConfigsServiceInterface $siteConfigService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SitesAdminServiceInterface $siteService, SiteConfigsServiceInterface $siteConfigService)
    {
        $this->setViewConditions('Site', ['default' => ['query' => [
            'limit' => $siteConfigService->getValue('admin_list_num'),
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

        $this->set($siteService->getViewVarsForIndex($this->paginate($siteService->getIndex($this->request->getQueryParams()))));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * サイト追加
     *
     * @checked
     * @unitTest
     * @note(value="インストーラーを実装してからテーマの保有するプラグインをインストールする処理を追加する")
     */
    public function add(SitesAdminServiceInterface $siteService)
    {
        if ($this->request->is('post')) {

            /*** Sites.beforeAdd ** */
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }

            try {
                $site = $siteService->create($this->request->getData());
                /*** Sites.afterAdd ***/
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $site
                ]);

                // TODO ucmitz 未実装のためコメントアウト
                /* >>>
                if (!empty($site->theme)) {
                    $this->BcManager->installThemesPlugins($site->theme);
                }
                <<< */

                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を追加しました。'), $site->display_name));
                return $this->redirect(['action' => 'edit', $site->id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $site = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        $this->set($siteService->getViewVarsForAdd($site ?? $siteService->getNew()));
    }

    /**
     * サイト情報編集
     *
     * @param $id
     * @checked
     * @unitTest
     * @note(value="インストーラーを実装してからテーマの保有するプラグインをインストールする処理を追加する")
     */
    public function edit(SitesAdminServiceInterface $siteService, $id)
    {
        if (!$id) {
            $this->notFound();
        }
        $site = $siteService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {

            /*** Sites.beforeEdit ** */
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }

            $beforeSite = clone $site;
            try {
                $site = $siteService->update($site, $this->request->getData());

                /*** Sites.afterEdit ***/
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $site
                ]);

                // TODO ucmitz 未実装のためコメントアウト
                /* >>>
                if (!empty($site->theme) && $beforeSite->theme !== $site->theme) {
                    $this->BcManager->installThemesPlugins($site->theme);
                }
                <<< */

                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を更新しました。'), $site->display_name));
                $this->redirect(['action' => 'edit', $id]);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($siteService->getViewVarsForEdit($site));
    }

    /**
     * 有効状態にする
     *
     * @param $siteId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(SitesServiceInterface $siteService, $siteId)
    {
        $site = $siteService->get($siteId);

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($siteService->publish($siteId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を公開しました。'),
                    $site->name));
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * 無効状態にする
     *
     * @param $siteId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(SitesServiceInterface $siteService, $siteId)
    {
        $site = $siteService->get($siteId);

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($siteService->unpublish($siteId)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を非公開にしました。'),
                    $site->name));
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * 削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SitesServiceInterface $siteService, $id)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $site = $siteService->get($id);
        try {
            if ($siteService->delete($id)) {
                if ($id == $this->request->getAttribute('currentSite')->id) {
                    $session = $this->request->getSession();
                    $session->delete('BcApp.Admin.currentSite');
                }
                $this->BcMessage->setSuccess(__d('baser', 'サイト: {0} を削除しました。', $site->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
