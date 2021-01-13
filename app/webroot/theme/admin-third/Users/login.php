<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 3.0.7
 * @license         https://basercms.net/license/index.html
 */

/**
 * [MYPAGE] ログイン
 */
$this->BcBaser->js(['admin/libs/credit', 'admin/startup'], false);
include BASER_VIEWS . 'Users' . DS . 'admin' . DS . 'login.php';
