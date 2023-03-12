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
use BcUploader\Service\UploaderConfigsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * アップローダープラグイン
 */
class UploaderConfigsController extends BcApiController
{

    /**
     * 取得API
     *
     * @param UploaderConfigsServiceInterface $service
     * @return void
     * @checked
     * @notodo
     * @unitTest
     */
    public function view(UploaderConfigsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'uploaderConfig' => $service->get()
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderConfig']);
    }

    /**
     * 保存API
     *
     * @param UploaderConfigsServiceInterface $service
     * @return void
     * @checked
     * @notodo
     * @unitTest
     */
    public function edit(UploaderConfigsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $uploaderConfig = $errors = null;
        try {
            $uploaderConfig = $service->update($this->request->getData());
            $message = __d('baser_core', 'アップローダープラグインを保存しました。', );
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'uploaderConfig' => $uploaderConfig,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderConfig', 'message', 'errors']);
    }

}
