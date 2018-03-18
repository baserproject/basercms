<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド 一覧　行
 * @var \BcAppView $this
 */
?>


<tr id="Row<?php echo $count + 1 ?>">
	<td class="row-tools" style="width:180px;">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['MailMessage']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['MailMessage']['id'])) ?>
		<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('alt' => __d('baser', '詳細'), 'class' => 'btn')), array('action' => 'view', $mailContent['MailContent']['id'], $data['MailMessage']['id']), array('title' => __d('baser', '詳細'), 'class' => 'btn-view')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => __d('baser', '削除'), 'class' => 'btn')), array('action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['MailMessage']['id']), array('title' => __d('baser', '削除'), 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['MailMessage']['id'] ?></td>
	<td><?php echo date('Y/m/d', strtotime($data['MailMessage']['created'])) ?></td>
	<td><?php echo date('H:i', strtotime($data['MailMessage']['created'])) ?></td>
	<td>
		<?php 
			$inData = array();
			$fileExists = false;
		?>
		<?php foreach ($mailFields as $mailField): ?>
			<?php if (!$mailField['MailField']['no_send'] && $mailField['MailField']['use_field']): ?>
				<?php
				if($mailField['MailField']['type'] != 'file') {
					$inData[] = h($this->Maildata->control(
						$mailField['MailField']['type'], 
						$data['MailMessage'][$mailField['MailField']['field_name']], 
						$this->Mailfield->getOptions($mailField['MailField'])
					));
				} else {
					if(!empty($data['MailMessage'][$mailField['MailField']['field_name']])) {
						$fileExists = true;
					}
				}
				?>
			<?php endif ?>
		<?php endforeach ?>
		<?php echo $this->Text->truncate(implode(',', $inData), 170) ?>
	</td>
	<td>
		<?php if($fileExists): ?>
			○
		<?php endif ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
</tr>
