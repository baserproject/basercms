<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * 400エラーページ
 * 呼出箇所：エラー発生時
 *
 * @var string $message エラーメッセージ
 * @var string $url URL
 */
?>


<h2 class="bs-error-title"><?php echo h($message) ?></h2>
<div class="bs-error-body">
	<strong><?php echo __('エラー'); ?>: </strong>
	<?php printf(
		__('アドレス %s に送信されたリクエストは無効です。'),
		"<strong>'{$url}'</strong>"
	); ?>
</div>
