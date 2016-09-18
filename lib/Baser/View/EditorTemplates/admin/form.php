<?php
/**
 * [ADMIN] エディタテンプレートー登録・編集
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


<?php $this->BcBaser->css('admin/ckeditor/editor', array('inline' => true)); ?>
<?php echo $this->BcForm->create('EditorTemplate', array('type' => 'file')) ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('EditorTemplate.id') ?>
					<?php echo $this->BcForm->input('EditorTemplate.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.name', 'テンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('EditorTemplate.name', array('type' => 'text', 'size' => 20, 'maxlength' => 50)) ?>
				<?php echo $this->BcForm->error('EditorTemplate.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.image', 'アイコン画像') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->file('EditorTemplate.image') ?>
				<?php echo $this->BcForm->error('EditorTemplate.image') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.description', '説明文') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('EditorTemplate.description', array('type' => 'textarea', 'cols' => 60, 'rows' => 2)) ?>
				<?php echo $this->BcForm->error('EditorTemplate.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('EditorTemplate.html', 'コンテンツ') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->ckeditor('EditorTemplate.html', array('editorWidth' => 'auto', 'editorUseTemplates' => false)) ?>
				<?php echo $this->BcForm->error('EditorTemplate.html') ?>
				<?php echo $this->BcForm->error('EditorTemplate.html') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="submit section">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php
		$this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('EditorTemplate.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('EditorTemplate.name')), false);
		?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>