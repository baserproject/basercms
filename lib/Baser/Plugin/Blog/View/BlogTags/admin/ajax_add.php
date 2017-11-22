<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if($result): ?>
	<?php echo $this->BcForm->input(
		'BlogTag.BlogTag',
		['type' => 'select', 'multiple' => 'checkbox', 'options' => $result, 'hiddenField' => false]);
	?>
<?php endif ?>