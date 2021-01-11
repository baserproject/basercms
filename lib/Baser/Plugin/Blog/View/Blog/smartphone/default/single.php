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
 * [PUBLISH] ブログ詳細ページ
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<!-- blog title -->
<h1 class="contents-head">
	<?php echo h($this->Blog->getTitle()) ?>
</h1>

<!-- post detail -->
<div class="post">

	<!-- post title -->
	<h2 class="contents-head">
		<?php $this->BcBaser->contentsTitle() ?><br/>
		<small><?php $this->Blog->postDate($post) ?></small>
	</h2>

	<?php $this->Blog->postContent($post) ?>

	<div class="meta"><span><?php $this->Blog->category($post) ?>&nbsp;<?php $this->Blog->author($post) ?></span></div>
	<?php $this->BcBaser->element('blog_tag', ['post' => $post]) ?>
</div>

<!-- contents navi -->
<div class="post-navi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->Blog->nextLink($post) ?>
</div>
<!-- comments -->
<?php $this->BcBaser->element('blog_comments') ?>
