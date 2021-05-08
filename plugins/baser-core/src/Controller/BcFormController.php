<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcFormController
 * @package BaserCore\Controller
 */
class BcFormController extends AppController
{

	/**
	 * セキュリティトークンを取得する
	 *
	 * @return mixed
     * @checked
     * @noTodo
	 */
	public function get_token()
	{
		$this->autoRender = false;
		return $this->response->withStringBody($this->request->getAttribute('csrfToken'));
	}

}
