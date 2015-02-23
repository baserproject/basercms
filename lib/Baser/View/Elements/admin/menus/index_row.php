<?php
/**
 * [ADMIN] グローバルメニュー一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if (!$data['Menu']['status']): ?>
	<?php $class = ' class="disablerow sortable"'; ?>
<?php else: ?>
	<?php $class = ' class="sortable"'; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
	<td class="row-tools">
		<?php if ($sortmode): ?>
			<span class="sort-handle"><?php $this->BcBaser->img('admin/sort.png', array('alt' => '並び替え')) ?></span>
			<?php
			echo $this->BcForm->input('Sort.id' . $data['Menu']['id'], array(
				'type' => 'hidden',
				'class' => 'id',
				'value' => $data['Menu']['id']))
			?>
		<?php endif ?>
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['Menu']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Menu']['id'])) ?>
		<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['Menu']['id']), array('title' => '編集')) ?>			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['Menu']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['Menu']['no']; ?></td>
	<td><?php $this->BcBaser->link($data['Menu']['name'], array('action' => 'edit', $data['Menu']['id'])); ?><br />
		<?php $this->BcBaser->link($data['Menu']['link'], $data['Menu']['link'], array('target' => '_blank')); ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['Menu']['created']); ?><br />
<?php echo $this->BcTime->format('Y-m-d', $data['Menu']['modified']); ?></td>
</tr>