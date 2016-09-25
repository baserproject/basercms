<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
?>

<script type="text/javascript">
$(window).load(function() {
	$("#UploaderCategoryName").focus();
});
</script>

<!-- form -->
<?php echo $this->BcForm->create('UploaderCategory') ?>
<?php echo $this->BcForm->input('UploaderCategory.id', array('type' => 'hidden')) ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th><?php echo $this->BcForm->label('UploaderCategory.id', 'NO') ?></th>
		<td>
			<?php echo $this->BcForm->value('UploaderCategory.id') ?>
			<?php echo $this->BcForm->input('UploaderCategory.id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th><?php echo $this->BcForm->label('UploaderCategory.name', 'カテゴリ名') ?>&nbsp;<span class="required">*</span></th>
		<td>
			<?php echo $this->BcForm->input('UploaderCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 50)) ?>
			<?php echo $this->BcForm->error('UploaderCategory.name') ?>
		</td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->submit('登　録', array('div' => false, 'class' => 'button')) ?>
<?php else: ?>
	<?php echo $this->BcForm->submit('更　新', array('div' => false, 'class' => 'button')) ?>
	<?php $this->BcBaser->link('削　除',
			array('action' => 'delete', $this->BcForm->value('UploaderCategory.id')),
			array('class' => 'submit-token button'),
			sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('UploaderCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
