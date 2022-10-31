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

namespace BaserCore\Controller\Api;

use BaserCore\Service\SitesServiceInterface;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SitesController
 */
class SitesController extends BcApiController
{

    /**
     * サイト情報取得
     * @param SitesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(SitesServiceInterface $service, $id)
    {
        $this->set([
            'site' => $service->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['site']);
    }

    /**
     * サイト情報一覧取得
     * @param SitesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SitesServiceInterface $service)
    {
        $this->set([
            'sites' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['sites']);
    }

    /**
     * サイト情報登録
     * @param SitesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(SitesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $site = $service->create($this->request->getData());
            $message = __d('baser', 'サイト「{0}」を追加しました。', $site->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $site = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'site' => $site,
            'errors' => $site->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'site', 'errors']);
    }

    /**
     * サイト情報編集
     * @param SitesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(SitesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $site = $service->get($id);
        try {
            $site = $service->update($site, $this->request->getData());
            $message = __d('baser', 'サイト「{0}」を更新しました。', $site->name);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'site' => $site,
            'errors' => $site->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['site', 'message', 'errors']);
    }

    /**
     * サイト情報削除
     * @param SitesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SitesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $site = $service->get($id);
        try {
            if ($service->delete($id)) {
                $message = __d('baser', 'サイト: {0} を削除しました。', $site->name);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'site' => $site
        ]);
        $this->viewBuilder()->setOption('serialize', ['site', 'message']);
    }

    /**
     * 選択可能なデバイスと言語の一覧を取得する
     *
     * @param SitesServiceInterface $service
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_selectable_devices_and_lang(SitesServiceInterface $service, $mainSiteId, $currentSiteId = null)
    {
        $this->set([
            'devices' => $service->getSelectableDevices($mainSiteId, $currentSiteId),
            'langs' => $service->getSelectableLangs($mainSiteId, $currentSiteId),
        ]);
        $this->viewBuilder()->setOption('serialize', ['devices', 'langs']);
    }

}
