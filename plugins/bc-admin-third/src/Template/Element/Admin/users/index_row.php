<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

?>


<tr>
	<td class="bca-table-listup__tbody-td"><?php echo $user->id ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($user->name, ['action' => 'edit', $user->id], ['escape' => true]) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($user->nickname) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcText->listValue('User.user_group_id', $user->user_group_id); ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($user->real_name_1) ?>
		&nbsp;<?php echo h($user->real_name_2) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($user) ?>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format('Y-m-d', $user->created) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $user->modified) ?></td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'edit', $user->id], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $user->id], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php if (!$this->BcBaser->isAdminUser($user->user_group_id)): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_agent_login', $user->id], ['title' => __d('baser', 'ログイン'), 'class' => 'btn-login bca-btn-icon', 'data-bca-btn-type' => 'switch', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
