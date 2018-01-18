<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [SMARTPHONE] ブログトップ
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription($this->Blog->getDescription());
?>


<!-- title -->
<h1 class="contents-head">
	<?php $this->Blog->title() ?>
</h1>

<!-- description -->
<?php if ($this->Blog->descriptionExists()): ?>
	<section class="blog-description">
		<?php $this->Blog->description() ?>
	</section>
<?php endif ?>

<section class="box news">
	<!-- list -->
	<?php if (!empty($posts)): ?>
		<ul>
			<?php foreach ($posts as $post): ?>
				<li><?php $this->Blog->postLink($post, '<span class="date">' . $this->Blog->getPostDate($post) . '</span><br />' . $this->Blog->getPostTitle($post)) ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p class="no-data">記事がありません。</p>
	<?php endif; ?>
</section>

<!-- pagination -->
<?php $this->BcBaser->pagination('simple'); ?>