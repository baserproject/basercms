<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー一覧　行
 */
?>


<tr>
	<td class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['User']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['User']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php if (!$this->BcBaser->isAdminUser($data['User']['user_group_id'])): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_login.png', array('alt' => 'ログイン', 'class' => 'btn')), array('action' => 'ajax_agent_login', $data['User']['id']), array('title' => 'ログイン', 'class' => 'btn-login')) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['User']['id'] ?></td>
	<td><?php $this->BcBaser->link($data['User']['name'], array('action' => 'edit', $data['User']['id'])) ?></td>
	<td><?php echo $data['User']['nickname'] ?></td>
	<td><?php echo $this->BcText->listValue('User.user_group_id', $data['User']['user_group_id']); ?><br />
		<?php echo $data['User']['real_name_1']; ?>&nbsp;<?php echo $data['User']['real_name_2'] ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['User']['created']) ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['User']['modified']) ?></td>
</tr>
