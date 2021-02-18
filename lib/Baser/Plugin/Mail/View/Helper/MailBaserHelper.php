<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View.Helper
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * MailBaserHelper
 *
 * テーマより利用される事を前提としたヘルパー。テーマで必要となる機能を提供する。
 *
 * @package Mail.View.Helper
 * @property CakeRequest $request
 */
class MailBaserHelper extends AppHelper
{

	/**
	 * 現在のページがメールプラグインかどうかを判定する
	 *
	 * @return bool
	 */
	public function isMail()
	{
		if (!Hash::get($this->request->params, 'Content.plugin')) {
			return false;
		}
		if (Hash::get($this->request->params, 'Content.plugin') !== 'Mail') {
			return false;
		}
		return true;
	}

}
