<?php
/**
 * ブログアーカイブ一覧（スマホ用）
 * 呼出箇所：カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->BcBaser->getContentsTitle() . __('のアーカイブ一覧です。'));
?>


<h2><?php $this->Blog->title() ?></h2>

<h3><?php $this->BcBaser->contentsTitle() ?></h3>

<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
<article class="post clearfix">
	<h4><?php $this->Blog->postTitle($post) ?></h4>
	<?php $this->Blog->eyeCatch($post, array('link' => false, 'width' => 100)) ?>
	<?php $this->Blog->postContent($post, false, false) ?>
	<div class="meta">
		<?php $this->Blog->category($post) ?>
		&nbsp;
		<?php $this->Blog->postDate($post) ?>
		&nbsp;
		<?php $this->Blog->author($post) ?>
		<?php $this->BcBaser->element('Blog.blog_tag', array('post' => $post)) ?>
	</div>
</article>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。'); ?></p>
<?php endif; ?>

<!-- /Elements/smartphone/paginations/simple.php -->
<?php $this->BcBaser->pagination('simple'); ?>