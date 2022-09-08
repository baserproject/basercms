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

/**
 * メールフォーム設定コントローラー
 *
 * @package Mail.Controller
 */
class MailConfigsController extends MailAppController
{

    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'MailConfigs';

    /**
     * モデル
     *
     * @var array
     */
    public $uses = ['BcMail.MailConfig'];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents'];

    /**
     * [ADMIN] メールフォーム設定
     *
     * @return void
     */
    public function admin_form()
    {
        if (empty($this->request->getData())) {
            $this->request = $this->request->withParsedBody($this->MailConfig->read(null, 1));
        } else {

            /* 更新処理 */
            if ($this->MailConfig->save($this->request->getData())) {
                $this->BcMessage->setInfo(__d('baser', 'メールフォーム設定を保存しました。'));
                $this->redirect(['action' => 'form']);
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        $this->setTitle(__d('baser', 'メールプラグイン基本設定'));
        $this->setHelp('mail_configs_form');
    }
}
