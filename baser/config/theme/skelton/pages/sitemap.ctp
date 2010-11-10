<!-- BaserPageTagBegin -->
<?php $baser->setTitle('サイトマップ') ?>
<?php $baser->setDescription('BaserCMS inc.のサイトマップページ') ?>
<?php $baser->editPage(4) ?>
<!-- BaserPageTagEnd -->
<h2 class="contents-head">
	サイトマップ</h2>
<h3 class="contents-head">
	公開ページ</h3>
<ul class="section">
	<li>
<?php $baser->link("ホーム","/") ?></li>
	<li>
<?php $baser->link("会社案内","/about") ?></li>
	<li>
<?php $baser->link("サービス","/service") ?></li>
	<li>
<?php $baser->link("新着情報","/news/") ?></li>
	<li>
<?php $baser->link("お問い合わせ","/contact/index") ?></li>
	<li>
<?php $baser->link("サイトマップ","/sitemap") ?></li>
</ul>
<h3 class="contents-head">
	非公開ページ</h3>
<ul class="section">
<li>
	<?php $baser->link("管理者ログイン","/admin/users/login") ?>	</li>
</ul>
<p class="customize-navi corner10">
	<small>公開する際には非公開ページは削除をおすすめします。</small>
</p>
