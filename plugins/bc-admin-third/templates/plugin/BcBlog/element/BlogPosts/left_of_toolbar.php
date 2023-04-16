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
 * @var \BaserCore\View\BcFrontAppView $this
 */
$content = $this->getRequest()->getAttribute('currentContent');
if(!$content) return;
?>


<div class="bca-toolbar__tools-button bca-toolbar__tools-button-add">
	<?php $this->BcBaser->link(__d('baser_core', '新規記事追加'), [
		'plugin' => 'BcBlog',
		'prefix' => 'Admin',
		'controller' => 'BlogPosts',
		'action' => 'add', $content->entity_id
	], ['class' => 'tool-menu']); ?>
</div>
