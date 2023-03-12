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
use BcCustomContent\Service\CustomContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentsController
 */
class CustomContentsController extends BcApiController
{
    /**
     * 一覧取得API
     *
     * @param CustomContentsServiceInterface $service
     */
    public function index(CustomContentsServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 単一データAPI
     *
     * @param CustomContentsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $customContent = $message = null;
        try {
            $customContent = $service->get($id, $this->request->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'customContent' => $customContent,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customContent']);
    }

    /**
     * カスタムコンテンツの新規追加
     *
     * @param CustomContentsServiceInterface $service
     */
    public function add(CustomContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser_core', 'カスタムコンテンツ「{0}」を追加しました。', $entity->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。\n");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'customContent' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'customContent',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * 編集API
     *
     * @param CustomContentsServiceInterface $service
     */
    public function edit(CustomContentsServiceInterface $service)
    {
        //todo 編集API
    }

    /**
     * 削除API
     *
     * @param CustomContentsServiceInterface $service
     */
    public function delete(CustomContentsServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * リストAPI
     *
     * @param CustomContentsServiceInterface $service
     */
    public function list(CustomContentsServiceInterface $service)
    {
        //todo リストAPI
    }
}
