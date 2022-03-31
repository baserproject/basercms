<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログカテゴリ 一覧　行
 */
$allowOwners = [];
if (isset($user['user_group_id'])) {
	$allowOwners = ['', $user['user_group_id']];
}
?>


<tr<?php echo $rowGroupClass ?>>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['BlogCategory']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">チェックする</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['BlogCategory']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['BlogCategory']['no'] ?></td>
	<td class="bca-table-listup__tbody-td">
		<?php if (in_array($data['BlogCategory']['owner_id'], $allowOwners) || $this->BcAdmin->isSystemAdmin()): ?>
			<?php $this->BcBaser->link($data['BlogCategory']['name'], ['action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']]) ?>
		<?php else: ?>
			<?php echo $data['BlogCategory']['name'] ?>
		<?php endif ?>
		<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
			<br/>
			<?php echo $this->BcText->arrayValue($data['BlogCategory']['owner_id'], $owners) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['BlogCategory']['title']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format('Y-m-d', $data['BlogCategory']['created']); ?>
		<br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogCategory']['modified']); ?></td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', $this->Blog->getCategoryUrl($data['BlogCategory']['id']), ['title' => __d('baser', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
		<?php if (in_array($data['BlogCategory']['owner_id'], $allowOwners) || (isset($user['user_group_id']) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId'))): ?>
			<?php $this->BcBaser->link('', ['action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']], ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
