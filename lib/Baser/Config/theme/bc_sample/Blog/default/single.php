<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログ記事詳細ページ
 * 呼出箇所：ブログ記事詳細ページ
 *
 * @var BcAppView $this
 * @var array $post ブログ記事データ
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<h2 class="bs-blog-title"><?php echo h($this->Blog->getTitle()) ?></h2>

<h3 class="bs-blog-post-title"><?php $this->BcBaser->contentsTitle() ?></h3>

<article class="bs-single-post">
	<div class="bs-single-post__meta">
		<?php $this->Blog->category($post, ['class' => 'bs-single-post__meta-category']) ?>
		<span class="bs-single-post__meta-date">
		<?php $this->Blog->postDate($post, 'Y.m.d') ?>
		</span>
	</div>
	<div class="bs-single-post__eye-catch">
		<?php $this->Blog->eyeCatch($post) ?>
	</div>
	<?php $this->Blog->postContent($post) ?>
</article>

<div class="bs-blog-contents-navi clearfix">
	<?php $this->Blog->prevLink($post) ?><?php $this->Blog->nextLink($post) ?>
</div>

<!-- /Elements/blog_related_posts.php -->
<?php $this->BcBaser->element('blog_related_posts') ?>

<!-- /Elements/blog_comennts.php -->
<?php $this->BcBaser->element('blog_comments') ?>
