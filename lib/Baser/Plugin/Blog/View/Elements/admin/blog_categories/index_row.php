<?php
/**
 * [ADMIN] ブログカテゴリ 一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$allowOwners = array();
if (isset($user['user_group_id'])) {
	$allowOwners = array('', $user['user_group_id']);
}
?>


<tr<?php echo $rowGroupClass ?>>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['BlogCategory']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogCategory']['id'])) ?>
		<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $this->Blog->getCategoryUrl($data['BlogCategory']['id']), array('title' => '確認', 'target' => '_blank')) ?>
		<?php if (in_array($data['BlogCategory']['owner_id'], $allowOwners) || (isset($user['user_group_id']) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId'))): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']), array('title' => '編集')) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['BlogCategory']['no'] ?></td>
	<td>
		<?php if (in_array($data['BlogCategory']['owner_id'], $allowOwners) || $this->BcAdmin->isSystemAdmin()): ?>
			<?php $this->BcBaser->link($data['BlogCategory']['name'], array('action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id'])) ?>
		<?php else: ?>
			<?php echo $data['BlogCategory']['name'] ?>
		<?php endif ?>
		<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
			<br />
			<?php echo $this->BcText->arrayValue($data['BlogCategory']['owner_id'], $owners) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['BlogCategory']['title'] ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['BlogCategory']['created']); ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogCategory']['modified']); ?></td>
</tr>
