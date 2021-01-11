<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事管理メニュー
 */
?>


<tr>
	<th><?php echo sprintf(__d('baser', '%s 管理メニュー'), strip_tags($this->request->params['Content']['title'])) ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(sprintf(__d('baser', '%s 設定'), strip_tags($this->request->params['Content']['title'])), ['controller' => 'blog_contents', 'action' => 'edit', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', '記事一覧'), ['controller' => 'blog_posts', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', '記事新規追加'), ['controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'カテゴリ一覧'), ['controller' => 'blog_categories', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'カテゴリ新規追加'), ['controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'タグ一覧'), ['controller' => 'blog_tags', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'タグ新規追加'), ['controller' => 'blog_tags', 'action' => 'add']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'コメント一覧'), ['controller' => 'blog_comments', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
		</ul>
	</td>
</tr>
