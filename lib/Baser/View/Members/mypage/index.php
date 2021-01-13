<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [MYPAGE] メンバー編集（デモ用）
 */
$authPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
?>


<h1><?php $this->BcBaser->contentsTitle() ?></h1>

<p><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $authPrefix['logoutAction']) ?>
