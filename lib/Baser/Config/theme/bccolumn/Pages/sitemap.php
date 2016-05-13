<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('サイトマップ') ?>
<?php $this->BcBaser->setDescription('baserCMS inc.のサイトマップページ') ?>
<?php $this->BcBaser->setPageEditLink(5) ?>
<!-- BaserPageTagEnd -->

<h2 class="contents-head">サイトマップ</h2>
<?php $this->BcBaser->sitemap() ?>
<ul class="section">
	<li><?php $this->BcBaser->link("新着情報","/news/index") ?></li>
	<li><?php $this->BcBaser->link("お問い合わせ","/contact/index") ?>	</li>
</ul>