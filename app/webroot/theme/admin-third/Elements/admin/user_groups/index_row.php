<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 0.1.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザーグループ一覧　行
 */
?>


<tr>
	<td class="bca-table-listup__tbody-td"><?php echo $data['UserGroup']['id'] ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['UserGroup']['name'], ['action' => 'edit', $data['UserGroup']['id']], ['escape' => true]) ?>
		<?php if (!empty($data['User'])): ?><br>
			<?php foreach($data['User'] as $user): ?>
				<span
					class="tag"><?php $this->BcBaser->link($this->BcBaser->getUserName($user), ['controller' => 'users', 'action' => 'edit', $user['id']], ['escape' => true]) ?></span>
			<?php endforeach ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['UserGroup']['title']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format('Y-m-d', $data['UserGroup']['created']) ?>
		<br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['UserGroup']['modified']) ?></td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($data['UserGroup']['name'] != 'admins'): ?>
			<?php $this->BcBaser->link('', ['controller' => 'permissions', 'action' => 'index', $data['UserGroup']['id']], ['title' => __d('baser', '制限'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'permission', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
		<?php $this->BcBaser->link('', ['action' => 'edit', $data['UserGroup']['id']], ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_copy', $data['UserGroup']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php if ($data['UserGroup']['name'] != 'admins'): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['UserGroup']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
