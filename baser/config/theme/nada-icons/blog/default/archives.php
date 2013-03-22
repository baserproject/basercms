<?php
/**
 * ブログアーカイブ一覧
 */
$bcBaser->css('colorbox/colorbox', array('inline' => true));
$bcBaser->js('jquery.colorbox-min-1.4.5', false);
$bcBaser->setDescription($blog->getTitle().'｜'.$bcBaser->getContentsTitle().'のアーカイブ一覧です。');
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
	<?php $bcBaser->contentsTitle() ?>
</h3>

<?php if(!empty($posts)): ?>
<?php foreach($posts as $post): ?>
<div class="post">
	<h4 class="contents-head">
		<?php $blog->postTitle($post) ?>
	</h4>
	<?php $blog->postContent($post,true,true) ?>
	<div class="meta"> 
	   <span class="date">
        <?php $blog->postDate($post) ?>
	   </span>
	   <span class="category">
		<?php $blog->category($post) ?>
		&nbsp;
		<?php $blog->author($post) ?>
		</span>
    </div>
	<?php $bcBaser->element('blog_tag', array('post' => $post)) ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>
<!-- pagination -->
<?php $bcBaser->pagination('simple'); ?>