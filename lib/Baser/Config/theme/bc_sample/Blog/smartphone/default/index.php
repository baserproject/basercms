<?php
/**
 * ブログトップ（スマホ版）
 * 呼出箇所：ブログトップ
 */
$this->BcBaser->setDescription($this->Blog->getDescription());
?>


<h2><?php $this->Blog->title() ?></h2>

<?php if ($this->Blog->descriptionExists()): ?>
<div class="blog-description"><?php $this->Blog->description() ?></div>
<?php endif ?>

<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
<article class="post clearfix">
	<?php $this->Blog->eyeCatch($post, array('link' => false, 'width' => 100)) ?>
	<h4><?php $this->Blog->postTitle($post) ?></h4>
	<?php $this->Blog->postContent($post, false, false, 90) ?>
	<div class="meta">
		<?php $this->Blog->category($post) ?>
		&nbsp;
		<?php $this->Blog->postDate($post) ?>
		&nbsp;
		<?php $this->Blog->author($post) ?>
		<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
	</div>
</article>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。'); ?></p>
<?php endif; ?>

<!-- /Elements/smartphone/paginations/simple.php -->
<?php $this->BcBaser->pagination('simple'); ?>