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

namespace BcUploader\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcUploader\Service\UploaderCategoriesServiceInterface;

/**
 * アップロードカテゴリコントローラー
 */
class UploaderCategoriesController extends BcApiController
{

    /**
     * 一覧取得API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     */
    public function index(UploaderCategoriesServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 新規追加API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     */
    public function add(UploaderCategoriesServiceInterface $service)
    {
        //todo 新規追加API
    }

    /**
     * 編集API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     */
    public function edit(UploaderCategoriesServiceInterface $service)
    {
        //todo 編集API
    }

    /**
     * 削除API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     */
    public function delete(UploaderCategoriesServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * コピーAPI
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     */
    public function copy(UploaderCategoriesServiceInterface $service)
    {
        //todo コピーAPI
    }

    /**
     * アップロードカテゴリのバッチ処理
     *
     * 指定したアップロードカテゴリに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名 'batch' が 'delete' 以外の値であれば500エラーを発生させる
     *
     * @param UploaderCategoriesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function batch(UploaderCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'アップロードカテゴリ「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
