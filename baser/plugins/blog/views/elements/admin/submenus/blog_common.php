<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ共通メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
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
	<th>ブログプラグイン共通メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $baser->link('ブログ一覧',array('controller'=>'blog_contents','action'=>'index')) ?></li>
			<li><?php $baser->link('新規ブログを登録',array('controller'=>'blog_contents','action'=>'add')) ?></li>
			<li><?php $baser->link('タグ一覧',array('controller'=>'blog_tags','action'=>'index')) ?></li>
			<li><?php $baser->link('新規タグを登録',array('controller'=>'blog_tags','action'=>'add')) ?></li>
		</ul>
	</td>
</tr>
