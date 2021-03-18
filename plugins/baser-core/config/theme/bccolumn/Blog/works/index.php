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

<div class="blog works works-index">
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<div class="post clearfix">
			<div class="category">
				<?php $this->Blog->category($post) ?>
			</div>
			<h2><?php $this->Blog->postTitle($post) ?></h2>
			<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
			<?php $uri = $this->BcBaser->getRoot().$this->request->params['Content']['name'].'/archives/'.$post['BlogPost']['no']; ?>
			<div class="eye-catch">
				<a href="<?php echo $uri ?>">
					<?php $this->Blog->eyeCatch($post, array('link'=>false, 'width'=>'150px', 'noimage'=>'/theme/bccolumn/img/blog/works/noimage.png')) ?>
				</a>
			</div>
			<?php $this->Blog->postContent($post, true, false, 100) ?>...
			<p class="more"><a href="<?php echo $uri ?>" class="btn btn-more"><?php echo __('詳細ページへ') ?></a></p>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。') ?></p>
<?php endif; ?>
<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>
</div>