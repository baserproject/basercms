<?php if($result): ?>
	<?php echo $this->BcForm->input(
		'BlogTag.BlogTag',
		array('type' => 'select', 'multiple' => 'checkbox', 'options' => $result, 'hidden' => false, 'value' => true));
	?>
<?php endif ?>