<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ管理メニュー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>固定ページ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('一覧を表示する', array('controller' => 'pages', 'action' => 'index')) ?></li>
<?php if($newCatAddable): ?>
			<li><?php $bcBaser->link('新規に登録する', array('controller' => 'pages', 'action' => 'add')) ?></li>
<?php endif ?>
			<li><?php $bcBaser->link('固定ページテンプレート読込', array('controller' => 'pages', 'action' => 'entry_page_files'), array('confirm' => 'テーマ '.Inflector::camelize($bcBaser->siteConfig['theme']).' フォルダ内のページテンプレートを全て読み込みます。\n本当によろしいですか？')) ?></li>
			<li><?php $bcBaser->link('固定ページテンプレート書出', array('controller' => 'pages', 'action' => 'write_page_files'), array('confirm' => 'データベース内のページデータを、'.'テーマ '.Inflector::camelize($bcBaser->siteConfig['theme']).' のページテンプレートとして全て書出します。\n本当によろしいですか？')) ?></li>
		</ul>
	</td>
</tr>
