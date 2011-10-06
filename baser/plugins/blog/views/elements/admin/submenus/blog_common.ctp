<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ共通メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>ブログプラグイン<br />共通メニュー</h2>
	<ul>
		<li><?php $baser->link('ブログ一覧',array('controller'=>'blog_contents','action'=>'index')) ?></li>
		<li><?php $baser->link('新規ブログを登録',array('controller'=>'blog_contents','action'=>'add')) ?></li>
		<li><?php $baser->link('タグ一覧',array('controller'=>'blog_tags','action'=>'index')) ?></li>
		<li><?php $baser->link('新規タグを登録',array('controller'=>'blog_tags','action'=>'add')) ?></li>
	</ul>
</div>
