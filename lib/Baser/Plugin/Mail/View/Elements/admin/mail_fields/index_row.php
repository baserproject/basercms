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
 * @var \BcAppView $this
 */
if (!$data['MailField']['use_field']) {
	$class = ' class="unpublish disablerow sortable"';
} else {
	$class = ' class="publish sortable"';
}
?>


<tr id="Row<?php echo $count + 1 ?>" <?php echo $class; ?>>
	<td style="width:25%" class="row-tools">
		<?php if ($sortmode): ?>
			<span
				class="sort-handle"><?php $this->BcBaser->img('admin/sort.png', ['alt' => __d('baser', '並び替え')]) ?></span>
			<?php echo $this->BcForm->hidden('Sort.id' . $data['MailField']['id'], ['class' => 'id', 'value' => $data['MailField']['id']]) ?>
		<?php endif ?>
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['MailField']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['MailField']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', ['alt' => __d('baser', '無効'), 'class' => 'btn']), ['action' => 'ajax_unpublish', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', ['alt' => __d('baser', '有効'), 'class' => 'btn']), ['action' => 'ajax_publish', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー'), 'class' => 'btn']), ['action' => 'ajax_copy', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td style="width:5%"><?php echo $data['MailField']['no'] ?></td>
	<td style="width:25%">
		<?php $this->BcBaser->link($data['MailField']['field_name'], ['action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id']]) ?>
		<br/>
		<?php echo h($data['MailField']['name']) ?>
	</td>
	<td style="width:15%"><?php echo $this->BcText->listValue('MailField.type', $data['MailField']['type']) ?></td>
	<td style="width:10%"><?php echo $data['MailField']['group_field'] ?></td>
	<td style="width:8%;text-align:center"><?php echo $this->BcText->booleanMark($data['MailField']['not_empty']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td style="width:12%;white-space:nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['MailField']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['MailField']['modified']) ?>
	</td>
</tr>
