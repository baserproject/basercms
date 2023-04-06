<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * ブログ記事詳細ページ
 *
 * 呼出箇所：ブログ記事詳細ページ
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post ブログ記事データ
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->setDescription($this->BcBaser->getBlogTitle() . '｜' . $this->BcBaser->getBlogPostContent($post, false, false, 50));
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
$this->BcBaser->setTitle($post->title);
?>


<h2 class="bs-blog-title"><?php echo h($this->BcBaser->getBlogTitle()) ?></h2>

<h3 class="bs-blog-post-title"><?php $this->BcBaser->contentsTitle() ?></h3>

<article class="bs-single-post">
	<div class="bs-single-post__meta">
		<?php $this->BcBaser->blogPostCategory($post, ['class' => 'bs-single-post__meta-category']) ?>
		<span class="bs-single-post__meta-date">
		<?php $this->BcBaser->blogPostDate($post, 'Y.m.d') ?>
		</span>
	</div>
	<div class="bs-single-post__eye-catch">
		<?php $this->BcBaser->blogPostEyeCatch($post) ?>
	</div>
	<?php $this->BcBaser->blogPostContent($post) ?>
</article>

<div class="bs-blog-contents-navi clearfix">
	<?php $this->BcBaser->blogPostPrevLink($post) ?><?php $this->BcBaser->blogPostNextLink($post) ?>
</div>

<!-- /Elements/blog_related_posts.php -->
<?php $this->BcBaser->element('blog_related_posts') ?>

<!-- /Elements/blog_comennts.php -->
<?php $this->BcBaser->element('blog_comments') ?>
