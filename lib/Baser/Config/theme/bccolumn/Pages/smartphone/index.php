<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('') ?>
<?php $this->BcBaser->setDescription('') ?>
<?php $this->BcBaser->setPageEditLink(7) ?>
<!-- BaserPageTagEnd -->

<div id="news" class="clearfix">
<div class="news" style="margin-right:28px;">
<h2 id="newsHead01">NEWS RELEASE</h2>
<div class="body">
<?php $this->BcBaser->blogPosts('news', 5) ?>
</div>
</div>
<div class="news">
<h2 id="newsHead02">baserCMS NEWS</h2>
<div class="body">
<?php $this->BcBaser->js('/s/feed/ajax/1') ?>
</div>
</div>
</div>