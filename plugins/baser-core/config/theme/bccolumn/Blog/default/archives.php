<?php
/**
 * ブログアーカイブ一覧
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->BcBaser->getContentsTitle() . __('のアーカイブ一覧です。'));
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade", maxWidth:"80%"});
	});
</script>

<div class="blog blog-archives">
<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<div class="post">
			<h3 class="contents-head">
			<?php $this->Blog->postTitle($post) ?>
			</h3>
		<?php $this->Blog->postContent($post, true, true) ?>
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
		<?php $this->BcBaser->element('Blog.blog_tag', array('post' => $post)) ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。') ?></p>
<?php endif; ?>
<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>
</div>