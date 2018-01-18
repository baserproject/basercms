<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事管理メニュー
 */
?>


<tr>
	<th><?php echo strip_tags($this->request->params['Content']['title']) ?>管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(strip_tags($this->request->params['Content']['title']) . '設定', ['controller' => 'blog_contents', 'action' => 'edit', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link('記事一覧', ['controller' => 'blog_posts', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link('記事新規追加', ['controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link('カテゴリ一覧', ['controller' => 'blog_categories', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link('カテゴリ新規追加', ['controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id']]) ?></li>
			<li><?php $this->BcBaser->link('タグ一覧', ['controller' => 'blog_tags', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link('タグ新規追加', ['controller' => 'blog_tags', 'action' => 'add']) ?></li>
			<li><?php $this->BcBaser->link('コメント一覧', ['controller' => 'blog_comments', 'action' => 'index', $blogContent['BlogContent']['id']]) ?></li>
		</ul>
	</td>
</tr>
