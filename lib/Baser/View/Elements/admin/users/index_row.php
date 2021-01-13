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
 * [ADMIN] ユーザー一覧　行
 */
?>


<tr>
	<td class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['User']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['User']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		<?php if (!$this->BcBaser->isAdminUser($data['User']['user_group_id'])): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_login.png', ['alt' => __d('baser', 'ログイン'), 'class' => 'btn']), ['action' => 'ajax_agent_login', $data['User']['id']], ['title' => __d('baser', 'ログイン'), 'class' => 'btn-login']) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['User']['id'] ?></td>
	<td><?php $this->BcBaser->link(h($data['User']['name']), ['action' => 'edit', $data['User']['id']]) ?></td>
	<td><?php echo h($data['User']['nickname']) ?></td>
	<td><?php echo h($this->BcText->listValue('User.user_group_id', $data['User']['user_group_id'])); ?><br/>
		<?php echo h($data['User']['real_name_1']); ?>&nbsp;<?php echo h($data['User']['real_name_2']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['User']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['User']['modified']) ?></td>
</tr>
