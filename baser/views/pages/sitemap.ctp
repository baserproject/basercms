<?php
/* SVN FILE: $Id$ */
/**
 * サイトマップ
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
 * @since			Baser v 0.1.0
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
	<li><a href="<?php $baser->root() ?>">ホーム</a></li>
	<li><a href="<?php $baser->root() ?>about.html">会社案内</a></li>
	<li><a href="<?php $baser->root() ?>service.html">サービス</a></li>
	<li><a href="<?php $baser->root() ?>news/">新着情報</a></li>
	<li><a href="<?php $baser->root() ?>contact/index">お問い合わせ</a></li>
	<li><a href="<?php $baser->root() ?>sitemap.html">サイトマップ</a></li>
</ul>
<h3 class="contents-head">非公開ページ</h3>
<ul class="section">
	<li><a href="<?php $baser->root() ?>admin/users/login">管理者ログイン</a></li>
</ul>
<p class="section"><small>※ このテンプレートを利用される場合、非公開ページの一覧は削除をおすすめします。</small></p>
