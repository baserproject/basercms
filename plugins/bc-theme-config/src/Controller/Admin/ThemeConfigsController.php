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
use BaserCore\Utility\BcUtil;

/**
 * Class ThemeConfigsController
 *
 * テーマ設定コントローラー
 *
 * @package Baser.Controller
 * @property ThemeConfig $ThemeConfig
 */
class ThemeConfigsController extends BcAdminAppController
{

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

    /**
     * [ADMIN] 設定編集
     */
    public function admin_form()
    {
        $this->setTitle(__d('baser', 'テーマ設定'));
        $this->setHelp('theme_configs_form');

        if (!$this->request->is(['post', 'put'])) {
            $this->request = $this->request->withParsedBody(['ThemeConfig' => $this->ThemeConfig->getKeyValue()]);
            return;
        }

        if (BcUtil::isOverPostSize()) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                    ini_get('post_max_size')
                )
            );
            $this->redirect(['action' => 'form']);
        }
        $this->ThemeConfig->set($this->request->getData());
        if (!$this->ThemeConfig->validates()) {
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            return;
        }

        $this->ThemeConfig->updateColorConfig($this->request->getData());
        $data = $this->ThemeConfig->saveImage($this->request->getData());
        $data = $this->ThemeConfig->deleteImage($data);
        foreach($data['ThemeConfig'] as $key => $value) {
            if (preg_match('/main_image_[0-9]_delete/', $key)) {
                unset($data['ThemeConfig'][$key]);
            }
        }
        if (!$this->ThemeConfig->saveKeyValue($data)) {
            $this->BcMessage->setError(__d('baser', '保存中にエラーが発生しました。'));
            return;
        }

        clearViewCache();
        $this->BcMessage->setInfo(__d('baser', 'システム設定を保存しました。'));
        $this->redirect(['action' => 'form']);
    }

}
