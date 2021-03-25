<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド 一覧　行
 */
?>


<tr id="Row<?php echo $count + 1 ?>">
	<td class="row-tools bca-table-listup__tbody-td">
		<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['MailMessage']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">チェックする</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['MailMessage']['id']]) ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td"><?php echo $data['MailMessage']['id'] ?></td>
	<td class="row-tools bca-table-listup__tbody-td"><?php echo date('Y/m/d H:i', strtotime($data['MailMessage']['created'])); ?></td>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php
		$inData = [];
		$fileExists = false;
		?>
		<?php foreach($mailFields as $mailField): ?>
			<?php if (!$mailField['MailField']['no_send'] && $mailField['MailField']['use_field']): ?>
				<?php
				if ($mailField['MailField']['type'] != 'file') {
					$inData[] = h($this->Maildata->control(
						$mailField['MailField']['type'],
						$data['MailMessage'][$mailField['MailField']['field_name']],
						$this->Mailfield->getOptions($mailField['MailField'])
					));
				} else {
					if (!empty($data['MailMessage'][$mailField['MailField']['field_name']])) {
						$fileExists = true;
					}
				}
				?>
			<?php endif ?>
		<?php endforeach ?>
		<?php echo $this->Text->truncate(implode(',', $inData), 170) ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php if ($fileExists): ?>
			○
		<?php endif ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'view', $mailContent['MailContent']['id'], $data['MailMessage']['id']], ['title' => __d('baser', '詳細'), 'class' => 'btn-view bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['MailMessage']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
</tr>
