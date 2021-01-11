<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

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
	public $uses = ['Mail.MailConfig'];

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
		if (empty($this->request->data)) {
			$this->request->data = $this->MailConfig->read(null, 1);
		} else {

			/* 更新処理 */
			if ($this->MailConfig->save($this->request->data)) {
				$this->BcMessage->setInfo(__d('baser', 'メールフォーム設定を保存しました。'));
				$this->redirect(['action' => 'form']);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		$this->pageTitle = __d('baser', 'メールプラグイン基本設定');
		$this->help = 'mail_configs_form';
	}

}
