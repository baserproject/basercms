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

use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;
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
     * @param SiteManageServiceInterface $siteManage
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SiteManageServiceInterface $siteManage)
    {
        $this->setViewConditions('Site', ['default' => ['query' => [
            'num' => $siteManage->getSiteConfig('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Sites.searchIndex
        $event = $this->getEventManager()->dispatch(new Event('Controller.Sites.searchIndex', $this, [
            'request' => $this->request
        ]));
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }

        $this->set('sites', $this->paginate($siteManage->getIndex($this->request->getQueryParams())));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * サイト追加
     */
    public function add(SiteManageServiceInterface $siteManage)
    {
        if ($this->request->is('post')) {

            /*** Sites.beforeAdd ** */
            $event = $this->getEventManager()->dispatch(new Event('Controller.Sites.beforeAdd', $this, [
                'data' => $this->request->getData()
            ]));
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }

            $site = $siteManage->create($this->request->getData());
            if (!$site->getErrors()) {
                /*** Sites.afterAdd ***/
                $this->getEventManager()->dispatch(new Event('Controller.Sites.afterAdd', $this, [
                    'site' => $site
                ]));

                // TODO 未実装のためコメントアウト
                /* >>>
                if (!empty($site->theme)) {
                    $this->BcManager->installThemesPlugins($site->theme);
                }
                <<< */

                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を追加しました。'), $site->name));
                return $this->redirect(['action' => 'edit', $site->id]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $site = $siteManage->getNew();
        }
        $this->set('site', $site);
    }

    /**
     * サイト情報編集
     *
     * @param $id
     */
    public function edit($id)
    {
        if (!$id) {
            $this->notFound();
        }
        if (!$this->request->getData()) {
//            $this->request->data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
            if (!$this->request->getData()) {
                $this->notFound();
            }
        } else {
            /*** Sites.beforeEdit ** */
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->request->getData()
            ]);
            if ($event !== false) {
                $this->request = $this->request->withParsedBody(($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult());
            }
            $beforeSite = $this->Site->find('first', ['conditions' => ['Site.id' => $this->request->getData('Site.id')]]);
            if ($data = $this->Site->save($this->request->getData())) {
                /*** Sites.afterEdit ***/
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $data
                ]);
                if (!empty($data['Site']['theme']) && $beforeSite['Site']['theme'] !== $data['Site']['theme']) {
                    $this->BcManager->installThemesPlugins($data['Site']['theme']);
                }
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」を更新しました。'), $this->request->getData('Site.name')));
                $this->redirect(['controller' => 'sites', 'action' => 'edit', $id]);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->setTitle(__d('baser', 'サイト編集'));
        $defaultThemeName = __d('baser', 'サイト基本設定に従う');
        if (!empty($this->siteConfigs['theme'])) {
            $defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
        }
        $themes = BcUtil::getThemeList();
        if (in_array($this->siteConfigs['theme'], $themes)) {
            unset($themes[$this->siteConfigs['theme']]);
        }
        $this->set('mainSites', $this->Site->getSiteList(null, ['excludeIds' => $this->request->getData('Site.id')]));
        $this->set('themes', array_merge(['' => $defaultThemeName], $themes));
        $this->setHelp('sites_form');
    }

    /**
     * 公開状態にする
     *
     * @param string $id
     * @return bool
     */
    public function ajax_unpublish($id)
    {
        $this->_checkSubmitToken();
        $this->autoRender = false;
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_changeStatus($id, false)) {
            $this->ajaxError(500, $this->Site->validationErrors);
            return false;
        }
        return true;
    }

    /**
     * 非公開状態にする
     *
     * @param string $id
     * @return bool
     */
    public function ajax_publish($id)
    {
        $this->_checkSubmitToken();
        $this->autoRender = false;
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_changeStatus($id, true)) {
            $this->ajaxError(500, $this->Site->validationErrors);
            return false;
        }
        return true;
    }

    /**
     * ステータスを変更する
     *
     * @param int $id
     * @param boolean $status
     * @return boolean
     */
    protected function _changeStatus($id, $status)
    {
        $statusTexts = [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')];
        $data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
        $data['Site']['status'] = $status;
        if (!$this->Site->save($data)) {
            return false;
        }

        $statusText = $statusTexts[$status];
        $this->BcMessage->setSuccess(
            sprintf(
                __d('baser', 'サイト「%s」 を、%s に設定しました。'),
                $data['Site']['name'],
                $statusText
            ),
            true,
            false
        );
        return true;
    }

    /**
     * 削除する
     */
    public function delete()
    {
        if (empty($this->request->getData('Site.id'))) {
            $this->notFound();
        }
        if (!$this->Site->delete($this->request->getData('Site.id'))) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            $this->redirect(['action' => 'edit', $this->request->getData('Site.id')]);
            return;
        }
        $this->BcMessage->setSuccess(sprintf(__d('baser', 'サイト「%s」 を削除しました。'), $this->request->getData('Site.name')));
        $this->redirect(['action' => 'index']);
    }

    /**
     * 選択可能なデバイスと言語の一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return string
     */
    public function ajax_get_selectable_devices_and_lang(SiteManageServiceInterface $sites, $mainSiteId, $currentSiteId = null)
    {
        $this->autoRender = false;
        $this->set([
            'devices' => $sites->getSelectableDevices($mainSiteId, $currentSiteId),
            'langs' => $sites->getSelectableLangs($mainSiteId, $currentSiteId),
        ]);
        $this->viewBuilder()->setOption('serialize', ['devices', 'langs']);
    }

}
