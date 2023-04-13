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

namespace BcThemeConfig\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemeConfigsController
 *
 * [API] テーマ設定コントローラー
 */
class ThemeConfigsController extends BcAdminApiController
{

    /**
     * [API] 取得
     *
     * @param ThemeConfigsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ThemeConfigsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'themeConfig' => $service->get()
        ]);
        $this->viewBuilder()->setOption('serialize', ['themeConfig']);
    }

    /**
     * [API] 保存
     *
     * @param ThemeConfigsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ThemeConfigsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $themeConfig = $errors = null;
        try {
            $themeConfig = $service->update($this->request->getData());
            $message = __d('baser_core', 'テーマ設定を保存しました。');
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'themeConfig' => $themeConfig,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['themeConfig', 'message', 'errors']);
    }

}
