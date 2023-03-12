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
     */
    public function edit(UploaderConfigsServiceInterface $service)
    {
        //todo 保存API
    }

}
