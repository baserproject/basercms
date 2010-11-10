<?php
/**
 * ブログ詳細ページ
 */
$baser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
$blog->editPost($post['BlogPost']['blog_content_id'],$post['BlogPost']['id']);
?>

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
