<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
?>
<!-- form -->
<?php echo $this->BcForm->create('UploaderCategory') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('UploaderCategory.id', ['type' => 'hidden']) ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<?php if ($this->action == 'admin_edit'): ?>
		<tr>
			<th><?php echo $this->BcForm->label('UploaderCategory.id', 'NO') ?></th>
			<td>
				<?php echo $this->BcForm->value('UploaderCategory.id') ?>
				<?php echo $this->BcForm->input('UploaderCategory.id', ['type' => 'hidden']) ?>
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<th><?php echo $this->BcForm->label('UploaderCategory.name', __d('baser', 'カテゴリ名')) ?>&nbsp;<span
				class="required">*</span></th>
		<td>
			<?php echo $this->BcForm->input('UploaderCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 50, 'autofocus' => true]) ?>
			<?php echo $this->BcForm->error('UploaderCategory.name') ?>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php if ($this->action == 'admin_add'): ?>
		<?php echo $this->BcForm->submit(__d('baser', '登録'), ['div' => false, 'class' => 'button']) ?>
	<?php else: ?>
		<?php echo $this->BcForm->submit(__d('baser', '更新'), ['div' => false, 'class' => 'button']) ?>
		<?php $this->BcBaser->link(__d('baser', '削除'),
			['action' => 'delete', $this->BcForm->value('UploaderCategory.id')],
			['class' => 'submit-token button'],
			sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('UploaderCategory.name')),
			false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
