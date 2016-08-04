<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>シングルブログ記事管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('記事一覧', array('controller' => 'single_blog_posts', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('新規記事追加', array('controller' => 'single_blog_posts', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>