<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー一覧　行
 */
?>

<tr>
	<td class="bca-table-listup__tbody-td"><?php echo $data['User']['id'] ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['User']['name'], ['action' => 'edit', $data['User']['id']], ['escape' => true]) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['User']['nickname']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($this->BcText->listValue('User.user_group_id', $data['User']['user_group_id'])); ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['User']['real_name_1']) ?>
		&nbsp;<?php echo h($data['User']['real_name_2']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format('Y-m-d', $data['User']['created']) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $data['User']['modified']) ?></td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'edit', $data['User']['id']], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['User']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php if (!$this->BcBaser->isAdminUser($data['User']['user_group_id'])): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_agent_login', $data['User']['id']], ['title' => __d('baser', 'ログイン'), 'class' => 'btn-login bca-btn-icon', 'data-bca-btn-type' => 'switch', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
