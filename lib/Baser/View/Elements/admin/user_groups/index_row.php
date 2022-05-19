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
 * [ADMIN] ユーザーグループ一覧　行
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($data['UserGroup']['name'] != 'admins'): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_permission.png', ['alt' => __d('baser', '制限'), 'class' => 'btn']), ['controller' => 'permissions', 'action' => 'index', $data['UserGroup']['id']], ['title' => __d('baser', '制限')]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['UserGroup']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー'), 'class' => 'btn']), ['action' => 'ajax_copy', $data['UserGroup']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy']) ?>
		<?php if ($data['UserGroup']['name'] != 'admins'): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['UserGroup']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['UserGroup']['id'] ?></td>
	<td><?php $this->BcBaser->link($data['UserGroup']['name'], ['action' => 'edit', $data['UserGroup']['id']], ['escape' => true]) ?>
		<?php if (!empty($data['User'])): ?><br/>
			<?php foreach($data['User'] as $user): ?>
				<span
					class="tag"><?php $this->BcBaser->link(h($this->BcBaser->getUserName($user)), ['controller' => 'users', 'action' => 'edit', $user['id']]) ?></span>
			<?php endforeach ?>
		<?php endif ?>
	</td>
	<td><?php echo h($data['UserGroup']['title']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['UserGroup']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['UserGroup']['modified']) ?></td>
</tr>
