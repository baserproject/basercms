<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('') ?>
<?php $this->BcBaser->setDescription('') ?>
<?php $this->BcBaser->setPageEditLink(1) ?>
<!-- BaserPageTagEnd -->

<div class="clearfix" id="NewsList">
<h2>新着情報</h2>
<?php $this->BcBaser->blogPosts('news', 5) ?></div>

<div id="BaserFeed">
<h2>baserCMS</h2>
<?php $this->BcBaser->js('/feed/ajax/1') ?></div>