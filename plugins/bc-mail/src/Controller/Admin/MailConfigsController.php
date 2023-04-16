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

namespace BcMail\Controller\Admin;

use BaserCore\Utility\BcUtil;
use BcMail\Service\MailConfigsServiceInterface;

/**
 * メールフォーム設定コントローラー
 *
 */
class MailConfigsController extends MailAdminAppController
{

    /**
     * [ADMIN] メールフォーム設定
     *
     * @return void
     */
    public function index(MailConfigsServiceInterface $service)
    {
        if ($this->request->is('post')) {
            $entity = $service->update($this->getRequest()->getData());
            if (!$entity->getErrors()) {
                $this->BcMessage->setInfo(__d('baser_core', 'メールプラグイン設定を保存しました。'));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
        } else {
            $entity = $service->get();
        }
        $this->set(['entity' => $entity]);
    }

}
