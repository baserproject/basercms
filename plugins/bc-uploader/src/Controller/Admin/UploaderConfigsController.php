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
     */
    public function index(UploaderConfigsServiceInterface $service)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            if($service->update($this->getRequest()->getData())) {
                $this->BcMessage->setSuccess(__d('baser', 'アップローダー設定を保存しました。'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set([
            'uploaderConfig' => $service->get()
        ]);
    }
}
