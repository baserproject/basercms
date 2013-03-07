<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アクセス制限設定一覧　行
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
?>


<?php if (!$data['Permission']['status']): ?>
		<?php $class=' class="disablerow unpublish sortable"'; ?>
<?php else: ?>
		<?php $class=' class="publish sortable"'; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
	<td style="width:15%" class="row-tools">
<?php if($sortmode): ?>
		<span class="sort-handle"><?php $bcBaser->img('sort.png', array('alt' => '並び替え')) ?></span>
		<?php echo $bcForm->input('Sort.id' . $data['Permission']['id'], array('type' => 'hidden', 'class' => 'id', 'value'=>$data['Permission']['id'])) ?>
<?php endif ?>
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['Permission']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Permission']['id'])) ?>
<?php endif ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '無効', 'class' => 'btn')), array('action' => 'ajax_unpublish', $data['Permission']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '有効', 'class' => 'btn')), array('action' => 'ajax_publish', $data['Permission']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $this->params['pass'][0], $data['Permission']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $this->params['pass'][0], $data['Permission']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
<?php if($data['Permission']['name']!='admins'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['Permission']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php endif ?>
	</td>
	<td style="width:10%"><?php echo $data['Permission']['no']; ?></td>
	<td style="width:55%">
		<?php $bcBaser->link($data['Permission']['name'], array('action' => 'edit', $this->params['pass'][0], $data['Permission']['id'])); ?><br />
		<?php echo $data['Permission']['url']; ?>
	</td>
	<td style="width:10%" class="align-center"><?php echo $bcText->arrayValue($data['Permission']['auth'], array(0 => '×', 1 => '〇')) ?></td>
	<td style="width:10%">
		<?php echo $bcTime->format('Y-m-d', $data['Permission']['created']); ?><br />
		<?php echo $bcTime->format('Y-m-d', $data['Permission']['modified']); ?>
	</td>
</tr>