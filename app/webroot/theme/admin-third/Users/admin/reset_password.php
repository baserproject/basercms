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
 * [ADMIN] パスワード再生成画面
 */
?>


<div class="section">
	<h2>新しいパスワードを発行しました。</h2>
	<p>
		新しいパスワード : <?= $this->get('new_password') ?>
	</p>
	<p>
		任意のパスワードを設定したい場合は
		<?php
			$this->BcBaser->link(
				__d('baser', 'アカウント設定'),
				[
					SessionHelper::read('Auth.Admin.id'),
					'admin' => true,
					'plugin' => null,
					'controller' => 'users',
					'action' => 'edit'
				]
			);
		?>で変更してください。
	</p>
</div>
