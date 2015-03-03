<?php
/**
 * [ADMIN] ブログ共通メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>ブログプラグイン共通メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('ブログ一覧', array('controller' => 'blog_contents', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('ブログ新規追加', array('controller' => 'blog_contents', 'action' => 'add')) ?></li>
			<li><?php $this->BcBaser->link('タグ一覧', array('controller' => 'blog_tags', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('タグ新規追加', array('controller' => 'blog_tags', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
