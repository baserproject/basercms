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
 * [PUBLISH] ブログアーカイブ一覧
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->BcBaser->getContentsTitle() . 'のアーカイブ一覧です。');
?>


<!-- title -->
<h1 class="contents-head">
	<?php $this->Blog->title() ?>
</h1>

<!-- archives title -->
<h2 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h2>

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