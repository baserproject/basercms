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

namespace BcCustomContent\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Service\CustomLinksServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomLinksController
 */
class CustomLinksController extends BcApiController
{

    /**
     * 一覧取得API
     *
     * @param CustomLinksServiceInterface $service
     */
    public function index(CustomLinksServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 単一データAPI
     *
     * @param CustomLinksServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomLinksServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');
        $customLink = $message = null;
        try {
            $customLink = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        }

        $this->set([
            'customLink' => $customLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customLink']);
    }

    /**
     * 新規追加API
     *
     * @param CustomLinksServiceInterface $service
     */
    public function add(CustomLinksServiceInterface $service)
    {
        //todo 新規追加API
    }

    /**
     * カスタムリンク編集API
     *
     * @param CustomLinksServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     */
    public function edit(CustomLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        try {
            $entity = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'カスタムリンク「{0}」を更新しました。', $entity->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $entity = $e->getEntity();
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'customLink' => $entity,
            'errors' => $entity->getErrors(),
        ]);

        $this->viewBuilder()->setOption('serialize', ['customLink', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param CustomLinksServiceInterface $service
     */
    public function delete(CustomLinksServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * リストAPI
     *
     * @param CustomLinksServiceInterface $service
     */
    public function list(CustomLinksServiceInterface $service)
    {
        //todo リストAPI
    }

    /**
     * カスタムリンクの親のリストを取得する
     *
     * @param CustomLinksServiceInterface $service
     * @param int $tableId
     */
    public function get_parent_list(CustomLinksServiceInterface $service, int $tableId)
    {
        $parentList = $service->getControlSource('parent_id', ['tableId' => $tableId]);
        $this->set(['parentList' => $parentList]);
        $this->viewBuilder()->setOption('serialize', ['parentList']);
    }

}
