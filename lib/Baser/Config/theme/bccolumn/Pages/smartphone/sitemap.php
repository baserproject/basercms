<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('サイトマップ') ?>
<?php $this->BcBaser->setDescription('baserCMS inc.のサイトマップページ') ?>
<?php $this->BcBaser->setPageEditLink(10) ?>
<!-- BaserPageTagEnd -->

<h2 class="contents-head">サイトマップ</h2>
<?php $this->BcBaser->sitemap() ?>
<ul class="section sitemap">
	<li><?php $this->BcBaser->link("新着情報","/s/news/index") ?></li>
	<li><?php $this->BcBaser->link("お問い合わせ","/s/contact/index") ?>	</li>
</ul>