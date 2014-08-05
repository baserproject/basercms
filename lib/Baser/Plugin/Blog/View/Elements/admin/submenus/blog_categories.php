<?php
/**
 * [ADMIN] ブログカテゴリ管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>カテゴリ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('ブログカテゴリ一覧', array('controller' => 'blog_categories', 'action' => 'index', $blogContent['BlogContent']['id'])) ?></li>
			<?php if (isset($newCatAddable) && $newCatAddable): ?>
				<li><?php $this->BcBaser->link('新規ブログカテゴリを登録', array('controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id'])) ?></li>
			<?php endif ?>
		</ul>
	</td>
</tr>