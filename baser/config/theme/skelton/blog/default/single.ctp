<?php
/**
 * ブログ詳細ページ
 */
$baser->css('colorbox/colorbox', null, null, false);
$baser->js('jquery.colorbox-min', false);
$baser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
$blog->editPost($post['BlogPost']['blog_content_id'],$post['BlogPost']['id']);
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>
<h3 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h3>
<div class="post">
	<?php $blog->postContent($post) ?>
	<div class="meta"> <span>
		<?php $blog->category($post) ?>
		&nbsp;
		<?php $blog->postDate($post) ?>
		&nbsp;
		<?php $blog->author($post) ?>
		</span> </div>
</div>
<div id="contentsNavi">
	<?php $blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $blog->nextLink($post) ?>
</div>
<?php $baser->element('blog_comments') ?>
