<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログタグ フォーム
 */
$this->BcBaser->js('Blog.admin/blog_tags/form', false);
?>


<!-- form -->
<?php echo $this->BcForm->create('BlogTag') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogTag.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('BlogTag.id') ?>
					<?php echo $this->BcForm->input('BlogTag.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogTag.name', __d('baser', 'ブログタグ名')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogTag.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcForm->error('BlogTag.name') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php
		$this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('BlogTag.id')], ['class' => 'submit-token button'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('BlogTag.name')), false);
		?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
