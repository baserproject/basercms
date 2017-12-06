<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * [MYPAGE] ログイン
 */
$this->BcBaser->js(['admin/libs/credit', 'admin/startup'], false);
include BASER_VIEWS . 'Users' . DS . 'admin' . DS . 'login.php';
