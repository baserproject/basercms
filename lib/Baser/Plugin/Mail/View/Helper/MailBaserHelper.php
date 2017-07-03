<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 4.0.5
 * @license			http://basercms.net/license/index.html
 */

/**
 * MailBaserHelper
 *
 * テーマより利用される事を前提としたヘルパー。テーマで必要となる機能を提供する。
 *
 * @package Mail.View.Helper
 * @property CakeRequest $request
 */
class MailBaserHelper extends AppHelper {

/**
 * 現在のページがメールプラグインかどうかを判定する
 *
 * @return bool
 */
	public function isMail() {
		return (!empty($this->request->params['Content']['plugin']) && $this->request->params['Content']['plugin'] == 'Mail');
	}
	
}