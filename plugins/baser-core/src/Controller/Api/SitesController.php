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

namespace BaserCore\Controller\Api;

use BaserCore\Service\SitesServiceInterface;
use Exception;

/**
 * Class SitesController
 * @package BaserCore\Controller\Api
 */
class SitesController extends BcApiController
{

    /**
     * サイト情報取得
     * @param SitesServiceInterface $sites
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(SitesServiceInterface $sites, $id)
    {
        $this->set([
            'site' => $sites->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['site']);
    }

    /**
     * サイト情報一覧取得
     * @param SitesServiceInterface $sites
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SitesServiceInterface $sites)
    {
        $this->set([
            'sites' => $this->paginate($sites->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['sites']);
    }

    /**
     * サイト情報登録
     * @param SitesServiceInterface $sites
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(SitesServiceInterface $sites)
    {
        $this->request->allowMethod(['post', 'delete']);
        $site = $sites->create($this->request->getData());
        if (!$site->getErrors()) {
            $message = __d('baser', 'サイト「{0}」を追加しました。', $site->name);
        } else {
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
     * @param SitesServiceInterface $sites
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(SitesServiceInterface $sites, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $site = $sites->update($sites->get($id), $this->request->getData());
        if (!$site->getErrors()) {
            $message = __d('baser', 'サイト「{0}」を更新しました。', $site->name);
        } else {
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
     * @param SitesServiceInterface $sites
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SitesServiceInterface $sites, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $site = $sites->get($id);
        try {
            if ($sites->delete($id)) {
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

}
