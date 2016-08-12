<!-- BaserPageTagBegin -->
<?php $this->BcBaser->setTitle('') ?>
<?php $this->BcBaser->setDescription('') ?>
<?php $this->BcBaser->setPageEditLink(6) ?>
<!-- BaserPageTagEnd -->

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">メインメニュー</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<span style="color:#8ABE08">■</span>
<?php $this->BcBaser->link("ニュースリリース",array("controller"=>"news","action"=>"index")) ?>
<br />
<span style="color:#8ABE08">■</span>
<?php $this->BcBaser->link("お問い合わせ",array("controller"=>"contact","action"=>"index")) ?>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">NEWS RELEASE</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php $this->BcBaser->blogPosts('news', 5) ?> <div>&nbsp;</div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">baserCMS NEWS</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php $this->BcBaser->feed(1) ?>