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

namespace BcThemeConfig\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcThemeConfig\Service\ThemeConfigsService;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemeConfigsController
 *
 * テーマ設定コントローラー
 */
class ThemeConfigsController extends BcAdminAppController
{

    /**
     * [ADMIN] 設定編集
     *
     * @param ThemeConfigsService $service
     * @checked
     * @noTodo
     */
    public function index(ThemeConfigsServiceInterface $service)
    {
        $entity = $service->get();
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->update($this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser', 'テーマ設定を保存しました。'));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        $this->set([
            'themeConfig' => $entity
        ]);
        $this->viewBuilder()->addHelper('BcThemeConfig.BcThemeConfig');
    }

}
