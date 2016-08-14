<?php
/**
 * [PUBLISH] ブログ詳細ページ
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>

<!-- blog title -->
<h2 class="contents-head">
	<?php $this->Blog->title() ?>
</h2>

<!-- post detail -->
<div class="post">

	<!-- post title -->
	<h3 class="contents-head">
		<?php $this->BcBaser->contentsTitle() ?><br />
		<small><?php $this->Blog->postDate($post) ?></small>
	</h3>

	<?php $this->Blog->postContent($post) ?>

	<div class="meta"><span><?php $this->Blog->category($post) ?>&nbsp;<?php $this->Blog->author($post) ?></span></div>
	<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
</div>

<!-- contents navi -->
<div id="contentsNavi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->Blog->nextLink($post) ?>
</div>
<!-- comments -->
<?php $this->BcBaser->element('blog_comments') ?>