<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('ContentLink.id') ?>
<table class="form-table">
	<tr>
		<th>URL</th>
		<td><?php echo $this->BcForm->input('ContentLink.url', array('type' => 'text', 'size' => 60)) ?></td>
	</tr>
</table>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>
<?php echo $this->BcForm->end() ?>
