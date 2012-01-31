<?php
/* SVN FILE: $Id$ */
/**
 * サイトマップ
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $baser->setTitle('サイトマップ') ?>
<?php $baser->setDescription('Baser CMS inc.のサイトマップページ') ?>

<h2 class="contents-head">サイトマップ</h2>
<h3 class="contents-head">公開ページ</h3>
<ul class="section">
	<li><?php $baser->link('ホーム', '/') ?></li>
	<li><?php $baser->link('会社案内', '/about') ?></li>
	<li><?php $baser->link('サービス', '/service') ?></li>
	<li><?php $baser->link('新着情報', '/news/index') ?></li>
	<li><?php $baser->link('お問い合わせ', '/contact/index') ?></li>
	<li><?php $baser->link('サイトマップ', '/sitemap') ?></li>
</ul>
<h3 class="contents-head">非公開ページ</h3>
<ul class="section">
	<li><?php $baser->link('管理者ログイン', array('controller' => 'users', 'action' => 'login')) ?></li>
</ul>
<p class="section"><small>※ このテンプレートを利用される場合、非公開ページの一覧は削除をおすすめします。</small></p>
