<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.1.0
 * @license         https://basercms.net/license/index.html
 */
/**
 * @var BcAppView $this
 */
?>


<div class="bca-toolbar__tools-button bca-toolbar__tools-button-add">
	<?php $this->BcBaser->link(__d('baser', '新規記事追加'), [
		'plugin' => 'blog',
		'admin' => true,
		'controller' => 'blog_posts',
		'action' => 'add', $this->request->params['Content']['entity_id']
	], ['class' => 'tool-menu']); ?>
</div>
