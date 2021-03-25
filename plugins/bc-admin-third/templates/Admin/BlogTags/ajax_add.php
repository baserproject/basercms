<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
?>


<?php if ($result): ?>
	<?php echo $this->BcForm->input('BlogTag.BlogTag', [
		'type' => 'select',
		'multiple' => 'checkbox',
		'options' => $result,
		'hiddenField' => false,
		'value' => true
	]); ?>
<?php endif ?>
