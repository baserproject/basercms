<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] リスト設定リンク
 */
$this->request->params['action'] = str_replace('mobile_', '', $this->request->params['action']);
include BASER_VIEWS . 'Elements' . DS . 'list_num' . $this->ext;
$this->request->params['action'] = 'mobile_' . $this->request->params['action'];