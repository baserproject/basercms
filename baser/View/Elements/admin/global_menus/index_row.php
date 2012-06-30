<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グローバルメニュー一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if (!$data['GlobalMenu']['status']): ?>
	<?php $class=' class="disablerow sortable"'; ?>
<?php else: ?>
	<?php $class=' class="sortable"'; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
	<td class="row-tools">
<?php if($sortmode): ?>
		<span class="sort-handle"><?php $this->BcBaser->img('sort.png',array('alt'=>'並び替え')) ?></span>
		<?php echo $this->BcForm->input('Sort.id'.$data['GlobalMenu']['id'], array(
				'type'	=> 'hidden',
				'class'	=> 'id',
				'value'	=> $data['GlobalMenu']['id'])) ?>
<?php endif ?>
<?php if($this->BcBaser->isAdminUser()): ?>
		<?php echo $this->BcForm->checkbox('ListTool.batch_targets.'.$data['GlobalMenu']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['GlobalMenu']['id'])) ?>
<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['GlobalMenu']['id']), array('title' => '編集')) ?>			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['GlobalMenu']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['GlobalMenu']['no']; ?></td>
	<td><?php $this->BcBaser->link($data['GlobalMenu']['name'], array('action' => 'edit', $data['GlobalMenu']['id'])); ?><br />
		<?php $this->BcBaser->link($data['GlobalMenu']['link'], $data['GlobalMenu']['link'], array('target'=>'_blank')); ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d',$data['GlobalMenu']['created']); ?><br />
	<?php echo $this->BcTime->format('Y-m-d',$data['GlobalMenu']['modified']); ?></td>
</tr>