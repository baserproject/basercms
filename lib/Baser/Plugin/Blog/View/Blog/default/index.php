<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ブログトップ
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription($this->Blog->getDescription());
?>

<script type="text/javascript">
	$(function () {
		if ($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition: "fade"});
	});
</script>

<!-- title -->
<h1 class="contents-head">
	<?php echo h($this->Blog->getTitle()) ?>
</h1>

<!-- description -->
<?php if ($this->Blog->descriptionExists()): ?>
	<div class="blog-description">
		<?php $this->Blog->description() ?>
	</div>
<?php endif ?>

<!-- list -->
<?php if (!empty($posts)): ?>
	<?php foreach($posts as $post): ?>
		<div class="post">
			<h3 class="contents-head">
				<?php $this->Blog->postTitle($post) ?>
			</h3>
			<?php $this->Blog->postContent($post, false, true) ?>
			<div class="meta"><span>
					<?php $this->Blog->category($post) ?>
					&nbsp;
					<?php $this->Blog->postDate($post) ?>
					&nbsp;
			<?php $this->Blog->author($post) ?>
				</span></div>
			<?php $this->BcBaser->element('blog_tag', ['post' => $post]) ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __d('baser', '記事がありません。') ?></p>
<?php endif; ?>

<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>
