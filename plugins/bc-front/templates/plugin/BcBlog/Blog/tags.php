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
 * タグ別記事一覧
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var string $tag
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription(sprintf(__d('baser_core', '%s のアーカイブ一覧です。'), $this->BcBaser->getContentsTitle()));
$this->BcBaser->setTitle($tag);
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
?>


<h2 class="bs-blog-title"><?php $this->BcBaser->contentsTitle() ?></h2>

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


<?php $this->BcBaser->pagination('simple'); ?>
