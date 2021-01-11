<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * テキスト
 * 呼出箇所：ウィジェット
 *
 * @var BcAppView $this
 * @var int $id ウィジェットID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @var string $text 登録したテキスト
 */
?>


<div class="bs-widget bs-widget-text bs-widget-text-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php echo $text ?>
</div>
