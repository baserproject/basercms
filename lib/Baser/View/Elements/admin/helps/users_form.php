<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー編集　ヘルプ
 */
?>


<ul>
	<li><?php echo __d('baser', 'ログイン用のユーザーアカウントを登録する事ができます。') ?></li>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<li><?php echo __d('baser', 'パスワード欄は変更する場合のみ入力します。') ?></li><?php endif ?>
</ul>
