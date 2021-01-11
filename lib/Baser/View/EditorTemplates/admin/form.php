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
 * [ADMIN] エディタテンプレートー登録・編集
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/editor_templates/form', false);
?>


<?php $this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]); ?>
<?php echo $this->BcForm->create('EditorTemplate', ['type' => 'file']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('EditorTemplate.id') ?>
					<?php echo $this->BcForm->input('EditorTemplate.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.name', __d('baser', 'テンプレート名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('EditorTemplate.name', ['type' => 'text', 'size' => 20, 'maxlength' => 50]) ?>
				<?php echo $this->BcForm->error('EditorTemplate.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.image', __d('baser', 'アイコン画像')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('EditorTemplate.image', ['type' => 'file']) ?>
				<?php echo $this->BcForm->error('EditorTemplate.image') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.description', __d('baser', '説明文')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('EditorTemplate.description', ['type' => 'textarea', 'cols' => 60, 'rows' => 2]) ?>
				<?php echo $this->BcForm->error('EditorTemplate.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.html', __d('baser', 'コンテンツ')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->ckeditor('EditorTemplate.html', ['editorWidth' => 'auto', 'editorUseTemplates' => false]) ?>
				<?php echo $this->BcForm->error('EditorTemplate.html') ?>
				<?php echo $this->BcForm->error('EditorTemplate.html') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit section">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php
		$this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('EditorTemplate.id')], ['class' => 'button submit-token', 'id' => 'BtnDelete'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('EditorTemplate.name')), false);
		?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
