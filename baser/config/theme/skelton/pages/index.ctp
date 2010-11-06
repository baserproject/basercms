<!-- BaserPageTagBegin -->
<?php $baser->setTitle('') ?>
<?php $baser->setDescription('') ?>
<?php $baser->editPage(1) ?>
<!-- BaserPageTagEnd -->
<?php echo $html->css("top",null,null,false) ?>


<div id="news" class="clearfix">
<div class="news" style="margin-right:28px;">
<h2 id="newsHead01">NEWS RELEASE</h2>
<div class="body">
<?php $baser->feed(1) ?>
</div>
</div>


<div class="news">
<h2 id="newsHead02">BaserCMS NEWS</h2>
<div class="body">
<?php $baser->feed(2) ?>
</div>
</div>
</div>