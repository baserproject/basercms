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
?>


	<h2><?php echo $message; ?></h2>
	<p class="error">
		<strong><?php echo __d('baser', 'エラー'); ?>: </strong>
		<?php printf(
			__d('baser', 'アドレス %s に送信されたリクエストは無効です。'),
			"<strong>'{$url}'</strong>"
		); ?>
	</p>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
