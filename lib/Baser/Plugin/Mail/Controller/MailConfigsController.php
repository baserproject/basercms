<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メールフォーム設定コントローラー
 *
 * @package Mail.Controller
 */
class MailConfigsController extends MailAppController {

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
	public $uses = array('Mail.MailConfig');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents');

/**
 * [ADMIN] メールフォーム設定
 *
 * @return void
 */
	public function admin_form() {
		if (empty($this->request->data)) {
			$this->request->data = $this->MailConfig->read(null, 1);
		} else {

			/* 更新処理 */
			if ($this->MailConfig->save($this->request->data)) {
				$this->setMessage(__d('baser', 'メールフォーム設定を保存しました。'));
				$this->redirect(array('action' => 'form'));
			} else {
				$this->setMessage(__d('baser', '入力エラーです。内容を修正してください。'), true);
			}
		}
		
		$this->pageTitle = __d('baser', 'メールプラグイン基本設定');
		$this->help = 'mail_configs_form';
	}

}
