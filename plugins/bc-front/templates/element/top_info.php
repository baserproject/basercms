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
 * トップページ NEWS 表示部分
 *
 * 呼出箇所：トップページ
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
?>


<div class="bs-info">
	<h2 class="bs-info__head">NEWS</h2>
	<?php $this->BcBaser->blogPosts('news', 5) ?>
</div>
