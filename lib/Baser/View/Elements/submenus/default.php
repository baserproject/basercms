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
 * [PUBLISH] サイドメニュー
 */
?>


<div class="sub-menu-contents">
	<h2><?php echo __d('baser', 'ログインメニュー') ?></h2>
	<ul>
		<li><?php $this->BcBaser->link(__d('baser', '管理者ログイン'), ['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login'], ['target' => '_blank']) ?></li>
	</ul>
</div>
