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
if (!$data['MailField']['use_field']) {
	$class = ' class="unpublish disablerow sortable"';
} else {
	$class = ' class="publish sortable"';
}
?>


<tr id="Row<?php echo $count + 1 ?>" <?php echo $class; ?>>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['MailField']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['MailField']['id']]) ?>
		<?php endif ?>
		<?php if ($sortmode): ?>
			<span class="sort-handle"><i class="bca-btn-icon-text"
										 data-bca-btn-type="draggable"></i><?php echo __d('baser', 'ドラッグ可能') ?></span>
			<?php echo $this->BcForm->hidden('Sort.id' . $data['MailField']['id'], ['class' => 'id', 'value' => $data['MailField']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['MailField']['no'] ?></td>
	<td class="bca-table-listup__tbody-td">
		<?php $this->BcBaser->link($data['MailField']['field_name'], ['action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id']]) ?>
		<br/>
		<?php echo h($data['MailField']['name']) ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcText->listValue('MailField.type', $data['MailField']['type']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['MailField']['group_field'] ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($data['MailField']['not_empty']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->BcTime->format('Y-m-d', $data['MailField']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['MailField']['modified']) ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'ajax_unpublish', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish bca-btn-icon', 'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_publish', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish bca-btn-icon', 'data-bca-btn-type' => 'publish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'edit', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_copy', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-icon--copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $mailContent['MailContent']['id'], $data['MailField']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
	</td>

</tr>
