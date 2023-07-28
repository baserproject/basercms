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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\Admin\SiteConfigsAdminServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Http\Response;

/**
 * Class SiteConfigsController
 * @uses SiteConfigsController
 */
class SiteConfigsController extends BcAdminAppController
{

    /**
     * 基本設定
     * @param SiteConfigsAdminServiceInterface $service
     * @return void|Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SiteConfigsAdminServiceInterface $service)
    {
        if ($this->request->is('post')) {
            $siteConfig = $service->update($this->getRequest()->getData());
            if (!$siteConfig->getErrors()) {
                BcUtil::clearAllCache();
                $this->BcMessage->setInfo(__d('baser_core', 'システム設定を保存しました。'));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
        } else {
            $siteConfig = $service->get();
        }
        $this->set($service->getViewVarsForIndex($siteConfig));
    }

}
