<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリ 一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$allowOwners = array();
if(isset($user['user_group_id'])) {
	$allowOwners = array('', $user['user_group_id']);
}
?>


<tr>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['BlogCategory']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogCategory']['id'])) ?>
<?php endif ?>		
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $blog->getCategoryUrl($data['BlogCategory']['id']), array('title' => '確認', 'target' => '_blank')) ?>
	<?php if(in_array($data['BlogCategory']['owner_id'], $allowOwners)||(isset($user['user_group_id']) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId'))): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $blogContent['BlogContent']['id'], $data['BlogCategory']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['BlogCategory']['no'] ?></td>
	<td>
<?php if(in_array($data['BlogCategory']['owner_id'], $allowOwners) || $bcAdmin->isSystemAdmin()): ?>
		<?php $bcBaser->link($data['BlogCategory']['name'], array('action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogCategory']['id'])) ?>
<?php else: ?>
		<?php echo $data['BlogCategory']['name'] ?>
<?php endif ?>
<?php if($bcBaser->siteConfig['category_permission']): ?>
		<br />
		<?php echo $bcText->arrayValue($data['BlogCategory']['owner_id'], $owners) ?>
<?php endif ?>
	</td>
	<td><?php echo $data['BlogCategory']['title'] ?></td>
	<td><?php echo $bcTime->format('Y-m-d',$data['BlogCategory']['created']); ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['BlogCategory']['modified']); ?></td>
</tr>
