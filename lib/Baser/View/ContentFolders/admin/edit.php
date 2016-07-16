<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('ContentFolder.id') ?>
<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('ContentFolder.content_template', 'コンテンツテンプレート') ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentFolder.content_template', array('type' => 'select', 'options' => $contentTemplates)) ?>
		</td>
	</tr>
</table>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>
<?php echo $this->BcForm->end() ?>
