<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('ContentFolder.id') ?>
<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('ContentFolder.folder_template', 'フォルダーテンプレート') ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentFolder.folder_template', array('type' => 'select', 'options' => $folderTemplateList)) ?>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('ContentFolder.page_template', '固定ページテンプレート') ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentFolder.page_template', array('type' => 'select', 'options' => $pageTemplateList)) ?>
		</td>
	</tr>
</table>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>
<?php echo $this->BcForm->end() ?>
