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
 * ブログトップ
 * 呼出箇所：ブログトップ
 *
 * @var BcAppView $this
 * @var array $posts ブログ記事リスト
 */
$this->BcBaser->setDescription($this->Blog->getDescription());
?>


<h2 class="bs-blog-title"><?php echo h($this->Blog->getTitle()) ?></h2>

<?php if ($this->Blog->descriptionExists()): ?>
<div class="bs-blog-description"><?php $this->Blog->description() ?></div>
<?php endif ?>

<section class="bs-blog-post">
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
	<article class="bs-blog-post__item clearfix">
		<?php if(!empty($post['BlogPost']['eye_catch'])): ?>
		<a href="<?php echo $this->Blog->getPostLinkUrl($post) ?>" class="bs-blog-post__item-eye-catch">
			<?php $this->Blog->eyeCatch($post, ['width' => 150, 'link' => false]) ?>
		</a>
		<?php endif ?>
		<span class="bs-blog-post__item-date"><?php $this->Blog->postDate($post, 'Y.m.d') ?></span>
		<?php $this->Blog->category($post, ['class' => 'bs-blog-post__item-category']) ?>
		<span class="bs-blog-post__item-title"><?php $this->Blog->postTitle($post) ?></span>
		<?php if(strip_tags($post['BlogPost']['content'] . $post['BlogPost']['detail'])): ?>
		<div class="bs-top-post__item-detail"><?php $this->Blog->postContent($post, true, false, 46) ?>...</div>
		<?php endif ?>
	</article>
	<?php endforeach; ?>
<?php else: ?>
<p class="bs-blog-no-data"><?php echo __('記事がありません。'); ?></p>
<?php endif ?>
</section>


<!-- /Elements/paginations/simple.php -->
<?php $this->BcBaser->pagination('simple'); ?>
