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


<tr id="Row<?php echo $count+1 ?>">
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['Message']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Message']['id'])) ?>
<?php endif ?>		
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '詳細', 'class' => 'btn')), array('action' => 'view', $mailContent['MailContent']['id'], $data['Message']['id']), array('title' => '詳細', 'class' => 'btn-view')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete',$mailContent['MailContent']['id'], $data['Message']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['Message']['id'] ?></td>
	<td><?php echo date('Y/m/d', strtotime($data['Message']['created'])) ?></td>
	<td><?php echo date('H:i', strtotime($data['Message']['created'])) ?></td>
	<td>
		<?php $inData = array() ?>
		<?php foreach($mailFields as $mailField): ?>
			<?php if(!$mailField['MailField']['no_send'] && $mailField['MailField']['use_field']): ?>
				<?php $inData[] = $maildata->control(
						$mailField['MailField']['type'],
						$data['Message'][$mailField['MailField']['field_name']],
						$mailfield->getOptions($mailField['MailField'])
				) ?>
			<?php endif ?>
		<?php endforeach ?>
		<?php echo $bcText->mbTruncate(implode(',',$inData),170) ?>
	</td>
</tr>
