<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールコンテンツ一覧　行
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

<?php if (!$data['MailContent']['status']): ?>
	<?php $class=' class="unpublish disablerow"'; ?>
<?php else: ?>
	<?php $class=' class="publish"'; ?>
<?php endif; ?>
<tr <?php echo $class; ?>>
	<td class="row-tools">
		<?php //echo $bcForm->checkbox('ListTool.batch_targets.'.$data['MailContent']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['MailContent']['id'])) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), '/'.$data['MailContent']['name'].'/index', array('title' => '確認', 'target' => '_blank')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), array('controller' => 'mail_fields', 'action' => 'index', $data['MailContent']['id']), array('title' => '管理')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['MailContent']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['MailContent']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['MailContent']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['MailContent']['id'] ?></td>
	<td><?php $bcBaser->link($data['MailContent']['name'], array('action' => 'edit', $data['MailContent']['id'])) ?></td>
	<td><?php echo $data['MailContent']['title'] ?></td>
	<td><?php echo $bcTime->format('Y-m-d',$data['MailContent']['created']) ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['MailContent']['modified']) ?></td>
</tr>