<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [MOBILE] ページネーション
 */
if (!empty($this->Paginator)) {
	$this->request->params['action'] = str_replace('mobile_', '', $this->request->params['action']);
	if ($this->Paginator->counter(['format' => '%pages%']) > 1) {
		echo $this->Paginator->prev('<<', null, null, null) . '&nbsp;';
		echo $this->Paginator->numbers(['separator' => '&nbsp;', 'modulus' => 4]) . '&nbsp;';
		echo $this->Paginator->next('>>', null, null, null);
	}
	$this->request->params['action'] = 'mobile_' . $this->request->params['action'];
}
