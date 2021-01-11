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
 * [ADMIN] エディタテンプレートメニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'エディタテンプレートメニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'エディタテンプレート一覧'), ['controller' => 'editor_templates', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'エディタテンプレート新規追加'), ['controller' => 'editor_templates', 'action' => 'add']) ?></li>
		</ul>
	</td>
</tr>
