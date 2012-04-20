<?php if($result): ?>
<?php echo $bcForm->input('BlogTag.BlogTag',
					array('type' => 'select', 'multiple' => 'checkbox', 'options' => $result, 'hidden' => false, 'value' => true)) ?>
<?php endif ?>