<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事管理メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>ブログ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('記事一覧', array('controller' => 'blog_posts','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
<?php if(isset($newCatAddable) && $newCatAddable): ?>
			<li><?php $bcBaser->link('新規記事を登録', array('controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id'])) ?></li>
<?php endif ?>			
			<li><?php $bcBaser->link('コメント一覧', array('controller' => 'blog_comments', 'action' => 'index', $blogContent['BlogContent']['id'])) ?></li>
			<li><?php $bcBaser->link('ブログ基本設定', array('controller' => 'blog_contents', 'action' => 'edit', $blogContent['BlogContent']['id'])) ?></li>
			<li><?php $bcBaser->link('公開ページ確認', '/'.$blogContent['BlogContent']['name'].'/index') ?></li>
		</ul>
	</td>
</tr>