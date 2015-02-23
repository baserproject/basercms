<?php
/**
 * [PUBLISH] ブログアーカイブ一覧
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->BcBaser->getContentsTitle() . 'のアーカイブ一覧です。');
?>

<!-- title -->
<h2 class="contents-head">
	<?php $this->Blog->title() ?>
</h2>

<!-- archives title -->
<h3 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h3>

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