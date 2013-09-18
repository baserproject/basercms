<?php
/**
 * [管理画面] ダッシュボードメニュー
 */
?>

<div class="side-navi">
	<h2>ダッシュボードメニュー</h2>
	<ul>
		<li><?php $bcBaser->link('ユーザーを追加する', array('controller' => 'users', 'action' => 'add')) ?></li>
		<li><?php $bcBaser->link('ニュースを投稿する', array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'add', 1)) ?></li>
		<li><?php $bcBaser->link('ページを作成する', array('controller' => 'pages', 'action' => 'add')) ?></li>
		<li><?php $bcBaser->link('お問い合わせ受信CSV', array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'download_csv', 1)) ?></li>
		<li><?php $bcBaser->link('ニュースコメント一覧', array('plugin' => 'blog', 'controller' => 'blog_comments', 'action' => 'index', 1)) ?></li>
	</ul>
</div>