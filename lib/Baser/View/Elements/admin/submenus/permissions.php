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
 * [ADMIN] パーミッション管理メニュー
 */
?>


<?php if ($usePermission): ?>
	<tr>
		<th><?php echo __d('baser', 'アクセス制限設定管理メニュー') ?></th>
		<td>
			<ul class="cleafix">
				<li><?php $this->BcBaser->link(__d('baser', 'アクセス制限設定一覧'), ['controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0]]) ?></li>
				<li><?php $this->BcBaser->link(__d('baser', 'アクセス制限設定新規追加'), ['controller' => 'permissions', 'action' => 'add', $this->request->params['pass'][0]]) ?></li>
			</ul>
		</td>
	</tr>
<?php endif; ?>
