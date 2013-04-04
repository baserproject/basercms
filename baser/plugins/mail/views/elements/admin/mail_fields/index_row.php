<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールフィールド 一覧　行
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


<?php if (!$data['MailField']['use_field']): ?>
	<?php $class=' class="unpublish disablerow sortable"'; ?>
<?php else: ?>
	<?php $class=' class="publish sortable"'; ?>
<?php endif; ?>
<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
	<td style="width:25%" class="row-tools">
<?php if($sortmode): ?>
		<span class="sort-handle"><?php $bcBaser->img('sort.png', array('alt' => '並び替え')) ?></span>
		<?php echo $bcForm->hidden('Sort.id' . $data['MailField']['id'], array('class' => 'id', 'value' => $data['MailField']['id'])) ?>
<?php endif ?>
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['MailField']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['MailField']['id'])) ?>
<?php endif ?>	
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '無効', 'class' => 'btn')), array('action' => 'ajax_unpublish', $mailContent['MailContent']['id'], $data['MailField']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '有効', 'class' => 'btn')), array('action' => 'ajax_publish', $mailContent['MailContent']['id'], $data['MailField']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $mailContent['MailContent']['id'], $data['MailField']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['MailField']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td style="width:5%"><?php echo $data['MailField']['no'] ?></td>
	<td style="width:25%">
		<?php $bcBaser->link($data['MailField']['field_name'], array('action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id'])) ?><br />
		<?php echo $data['MailField']['name'] ?>
	</td>
	<td style="width:15%"><?php echo $bcText->listValue('MailField.type',$data['MailField']['type']) ?></td>
	<td style="width:10%"><?php echo $data['MailField']['group_field'] ?></td>
	<td style="width:8%;text-align:center"><?php echo $bcText->booleanMark($data['MailField']['not_empty']) ?></td>
	<td style="width:12%;white-space:nowrap">
			<?php echo $bcTime->format('Y-m-d',$data['MailField']['created']) ?><br />
			<?php echo $bcTime->format('Y-m-d',$data['MailField']['modified']) ?>
	</td>
</tr>
