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
 * ブログトップ
 *
 * 呼出箇所：ブログトップ
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var array $posts ブログ記事リスト
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->setDescription($this->Blog->getDescription());
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
$this->BcBaser->setTitle($this->BcBaser->getBlogTitle());
?>


<h2 class="bs-blog-title"><?php echo h($this->BcBaser->getBlogTitle()) ?></h2>

<?php if ($this->BcBaser->blogDescriptionExists()): ?>
<div class="bs-blog-description"><?php $this->BcBaser->blogDescription() ?></div>
<?php endif ?>

<section class="bs-blog-post">
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
	<article class="bs-blog-post__item clearfix">
		<?php if(!empty($post->eye_catch)): ?>
		<a href="<?php echo $this->BcBaser->getBlogPostLinkUrl($post) ?>" class="bs-blog-post__item-eye-catch">
			<?php $this->BcBaser->blogPostEyeCatch($post, ['width' => 150, 'link' => false]) ?>
		</a>
		<?php endif ?>
		<span class="bs-blog-post__item-date"><?php $this->BcBaser->blogPostDate($post, 'Y.m.d') ?></span>
		<?php $this->BcBaser->blogPostCategory($post, ['class' => 'bs-blog-post__item-category']) ?>
		<span class="bs-blog-post__item-title"><?php $this->BcBaser->blogPostTitle($post) ?></span>
		<?php if(strip_tags($post->content . $post->detail)): ?>
		<div class="bs-top-post__item-detail"><?php $this->BcBaser->blogPostContent($post, true, false, 46) ?>...</div>
		<?php endif ?>
	</article>
	<?php endforeach; ?>
<?php else: ?>
<p class="bs-blog-no-data"><?php echo __d('baser_core', '記事がありません。'); ?></p>
<?php endif ?>
</section>


<!-- /Elements/paginations/simple.php -->
<?php $this->BcBaser->pagination('simple'); ?>
