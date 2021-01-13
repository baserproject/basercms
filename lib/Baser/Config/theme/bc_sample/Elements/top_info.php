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
 * トップページ NEWS 表示部分
 * 呼出箇所：トップページ
 *
 * @var BcAppView $this
 */
?>


<div class="bs-info">
	<h2 class="bs-info__head">NEWS</h2>
	<?php $this->BcBaser->blogPosts('news', 5) ?>
</div>
