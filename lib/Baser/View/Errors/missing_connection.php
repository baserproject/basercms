<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] missing class
 */
?>


	<h2><?php echo __d('cake_dev', 'Missing Database Connection'); ?></h2>
	<p class="error">
		<strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
		<?php echo __d('cake_dev', 'A Database connection using "%s" was missing or unable to connect.', h($class)); ?>
		<br/>
		<?php
		if (isset($message)):
			echo __d('cake_dev', 'The database server returned this error: %s', h($message));
		endif;
		?>
	</p>
<?php if (!$enabled) : ?>
	<p class="error">
		<strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
		<?php echo __d('cake_dev', '%s driver is NOT enabled', h($class)); ?>
	</p>
<?php endif; ?>
	<p class="notice">
		<strong><?php echo __d('cake_dev', 'Notice'); ?>: </strong>
		<?php echo __d('cake_dev', 'If you want to customize this error message, create %s', APP_DIR . DS . 'View' . DS . 'Errors' . DS . basename(__FILE__)); ?>
	</p>

<?php
echo $this->element('exception_stack_trace');
