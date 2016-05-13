<?php
/**
 * ブログトップ
 */
$this->BcBaser->css('admin/colorbox/colorbox', array('inline' => false));
$this->BcBaser->js('admin/jquery.colorbox-min-1.4.5', false);
$this->BcBaser->setDescription($this->Blog->getDescription());
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
	});
</script>

<div class="blog topics topics-index">
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<div class="post">
			<h2><?php $this->Blog->postTitle($post) ?></h2>
			<?php $uri = $this->BcBaser->getRoot().$post['BlogContent']['name'].'/archives/'.$post['BlogPost']['no']; ?>
			<div class="eye-catch">
				<a href="<?php echo $uri ?>">
					<?php $this->Blog->eyeCatch($post, array('link'=>false, 'width'=>'80px')) ?>
				</a>
			</div>
			<?php $this->Blog->postContent($post, true, false, 100) ?>...
			<p class="more"><a href="<?php echo $uri ?>" class="btn btn-more">詳細ページへ</a></p>

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
	<p class="no-data">記事がありません。</p>
<?php endif; ?>
<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>
</div>