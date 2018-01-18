<?php
/**
 * ブログトップ
 */
$this->BcBaser->setDescription($this->Blog->getDescription());
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade", maxWidth:"80%"});
	});
</script>

<div class="blog blog-index">
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<div class="post">
			<h2><?php $this->Blog->postTitle($post) ?></h2>
			<?php $this->Blog->postContent($post, false, __('詳細ページへ')) ?>
			<div class="meta"> 
				<span class="date">
					<?php $this->Blog->postDate($post) ?>
				</span>
				<span class="category">
					<?php $this->Blog->category($post) ?>
					&nbsp;
					<?php $this->Blog->author($post) ?>
				</span>
			</div>
		<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。') ?></p>
<?php endif; ?>
<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>
</div>