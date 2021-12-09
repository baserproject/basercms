<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * 404エラーページ
 * 呼出箇所：エラー発生時
 *
 * @var BcAppView $this
 * @var string $message エラーメッセージ
 * @var string $url URL
 */
?>


<h2 class="bs-error-title"><?php echo $message; ?></h2>
<div class="bs-error-body">
	<strong><?php echo __('エラー'); ?>: </strong>
	<?php printf(
		__('アドレス %s に送信されたリクエストは無効です。'),
		"<strong>'{$url}'</strong>"
	); ?>
</div>
<?php
if (Configure::read('debug') > 0):
	/* /lib/Cake/View/Elements/exception_stack_trace.ctp */
	echo $this->element('exception_stack_trace');
endif;
