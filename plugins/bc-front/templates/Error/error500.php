<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 500エラーページ
 * 呼出箇所：エラー発生時
 *
 * @var string $message エラーメッセージ
 * @var string $url URL
 */
?>


<h2 class="bs-error-title"><?php echo $message ?></h2>
<div class="bs-error-body">
	<strong><?php echo __('エラー'); ?>: </strong>
	<?php printf(
		__('アドレス %s に送信されたリクエストは無効です。'),
		"<strong>'{$url}'</strong>"
	); ?>
</div>
