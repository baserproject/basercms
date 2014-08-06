<?php
/**
 * [ADMIN] メールフィールド 一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr id="Row<?php echo $count + 1 ?>">
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['Message']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Message']['id'])) ?>
		<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '詳細', 'class' => 'btn')), array('action' => 'view', $mailContent['MailContent']['id'], $data['Message']['id']), array('title' => '詳細', 'class' => 'btn-view')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['Message']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['Message']['id'] ?></td>
	<td><?php echo date('Y/m/d', strtotime($data['Message']['created'])) ?></td>
	<td><?php echo date('H:i', strtotime($data['Message']['created'])) ?></td>
	<td>
		<?php $inData = array() ?>
		<?php foreach ($mailFields as $mailField): ?>
			<?php if (!$mailField['MailField']['no_send'] && $mailField['MailField']['use_field']): ?>
				<?php
				$inData[] = h($this->Maildata->control(
					$mailField['MailField']['type'], $data['Message'][$mailField['MailField']['field_name']], $this->Mailfield->getOptions($mailField['MailField'])
					))
				?>
			<?php endif ?>
		<?php endforeach ?>
		<?php echo $this->Text->truncate(implode(',', $inData), 170) ?>
	</td>
</tr>
