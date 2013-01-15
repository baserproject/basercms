<?php
/**
 * ブログ詳細ページ
 */
$this->bcBaser->css('colorbox/colorbox', array('inline' => true));
$this->bcBaser->js('jquery.colorbox-min', false);
$this->bcBaser->setDescription($this->blog->getTitle().'｜'.$this->blog->getPostContent($post,false,false,50));
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<h2 class="contents-head">
	<?php $this->blog->title() ?>
</h2>
<h3 class="contents-head">
	<?php $this->bcBaser->contentsTitle() ?>
</h3>
<div class="post">
	<?php $this->blog->postContent($post) ?>
	<div class="meta"> <span>
		<?php $this->blog->category($post) ?>
		&nbsp;
		<?php $this->blog->postDate($post) ?>
		&nbsp;
		<?php $this->blog->author($post) ?>
		</span> </div>
	<?php $this->bcBaser->element('blog_tag', array('post' => $post)) ?>
</div>
<div id="contentsNavi">
	<?php $this->blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->blog->nextLink($post) ?>
</div>
<?php $this->bcBaser->element('blog_comments') ?>