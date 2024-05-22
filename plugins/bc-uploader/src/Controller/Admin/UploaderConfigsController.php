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

namespace BcUploader\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcUploader\Service\UploaderConfigsService;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ファイルアップローダーコントローラー
 */
class UploaderConfigsController extends BcAdminAppController
{

    /**
     * [ADMIN] アップローダー設定
     *
     * @param UploaderConfigsService $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UploaderConfigsServiceInterface $service)
    {
        $uploaderConfig = $service->get();
        if($this->getRequest()->is(['post', 'put'])) {
            $uploaderConfig = $service->update($this->getRequest()->getData());
            if (!$uploaderConfig->getErrors()) {
                $this->BcMessage->setSuccess(__d('baser_core', 'アップローダー設定を保存しました。'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set([
            'uploaderConfig' => $uploaderConfig
        ]);
    }
}
