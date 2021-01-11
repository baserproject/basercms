<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			https://basercms.net/license/index.html
 */

/**
 * [ADMIN] パスワード再生成画面
 */
?>
<h3><?= __d('baser', '新しいパスワードを生成しました。') ?></h3>
<div class="section">
	<p>
		新しいパスワード : <?= $this->get('new_password') ?>
	</p>
</div>
