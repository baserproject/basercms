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
 * テキスト
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BaserCore\View\BcFrontAppView $this
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
